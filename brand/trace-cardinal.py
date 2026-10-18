"""Trace the logo cardinal into the specimen plate and the nest scene.

Run on the build host with:
uv run --with vtracer==1.0.0a4 --with pillow python brand/trace-cardinal.py OUT_DIR

Writes OUT_DIR/cardinal.svg (animated plate) and OUT_DIR/cardinal-field.svg
(nest scene), plus the intermediate trace inputs for inspection.

Polygons are in "logo space": brand/logo.png scaled to 2000px wide.
"""

import re
import sys
from pathlib import Path

from PIL import Image, ImageChops, ImageDraw, ImageFilter
import vtracer


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "brand/logo.png"
FIELD = ROOT / "preview/assets/cardinal-field.svg"
OUT = Path(sys.argv[1]).resolve()
OUT.mkdir(parents=True, exist_ok=True)

# Work area in logo space and the work-pixels-per-logo-pixel factor.
CROP = (270, 190, 1450, 1290)
WORK = 1.2

HEAD = [(960, 170), (1450, 170), (1450, 720), (1300, 700), (1200, 640), (1150, 540), (1108, 472), (995, 466), (960, 450)]
HEAD_CUT = HEAD[3:8]
GOGGLES_AND_BIB = [(1095, 385), (1250, 375), (1400, 385), (1400, 520), (1300, 560), (1280, 665), (1180, 650), (1150, 520), (1090, 480)]
LENS = [(1123, 438), (1154, 417), (1233, 395), (1283, 386), (1336, 388), (1366, 398), (1380, 418), (1378, 442), (1352, 461), (1307, 472), (1264, 493), (1215, 512), (1166, 518), (1126, 509), (1114, 478)]
BODY_SEED = (900, 700)
TAIL = [(200, 900), (530, 925), (690, 1020), (700, 1300), (200, 1300)]
TAIL_CUT = TAIL[1:3]
FEET = [
    [(935, 950), (1005, 945), (1065, 1005), (1075, 1105), (1015, 1115), (965, 1060), (940, 1010)],
    [(1070, 950), (1135, 955), (1205, 1005), (1220, 1075), (1175, 1085), (1125, 1045), (1085, 1000)],
]
PERCH = [(845, 1150), (1100, 1000), (1430, 905), (1440, 925), (1130, 1062), (940, 1235), (870, 1222)]

PIVOTS = {"head": (1150, 545), "tail": (610, 975), "body": (1080, 1065)}


def work(points):
    return [((x - CROP[0]) * WORK, (y - CROP[1]) * WORK) for x, y in points]


def poly_mask(size, *polygons):
    mask = Image.new("L", size)
    draw = ImageDraw.Draw(mask)
    for polygon in polygons:
        draw.polygon(work(polygon), fill=255)
    return mask


def band_mask(size, polyline, width):
    mask = Image.new("L", size)
    ImageDraw.Draw(mask).line(work(polyline), fill=255, width=int(width), joint="curve")
    return mask


def where(mask, fn):
    """Binary mask from a per-pixel RGBA predicate."""
    out = Image.new("L", mask.size)
    out.putdata([255 if fn(p) else 0 for p in mask.get_flattened_data()])
    return out


def fill_holes(mask):
    """Add regions the mask fully encloses (goggle glints, barrel highlights)."""
    marked = mask.copy()
    ImageDraw.floodfill(marked, (0, 0), 128, thresh=0)
    return ImageChops.lighter(mask, marked.point(lambda v: 255 if v == 0 else 0))


source = Image.open(SOURCE).convert("RGBA")
scale = source.width / 2000
box = tuple(round(v * scale) for v in CROP)
size = (round((CROP[2] - CROP[0]) * WORK), round((CROP[3] - CROP[1]) * WORK))
art = source.crop(box).resize(size, Image.Resampling.LANCZOS)

opaque = lambda p: p[3] > 128
is_red = where(art, lambda p: opaque(p) and p[0] > 120 and p[0] > p[1] * 1.45 and p[0] > p[2] * 1.3)
not_white = where(art, lambda p: opaque(p) and min(p[:3]) < 215)
is_dark = where(art, lambda p: opaque(p) and max(p[:3]) < 120)
not_gray_rule = where(art, lambda p: not (90 < p[0] < 215 and abs(p[0] - p[1]) < 18 and abs(p[1] - p[2]) < 18))

# The bird: red plumage plus the ink that hugs it, the goggles and bib, and the dark feet.
near_red = is_red.filter(ImageFilter.MaxFilter(23))
bird = ImageChops.multiply(ImageChops.multiply(near_red, not_white), not_gray_rule)
bird = ImageChops.lighter(bird, ImageChops.multiply(poly_mask(size, GOGGLES_AND_BIB), not_white))
bird = ImageChops.lighter(bird, ImageChops.multiply(poly_mask(size, *FEET), is_dark))
bird = ImageChops.lighter(bird, ImageChops.multiply(poly_mask(size, LENS), where(art, opaque)))
bird = fill_holes(bird).filter(ImageFilter.MedianFilter(3))
# Keep the one shape connected to the body; this drops stray lettering.
ImageDraw.floodfill(bird, tuple(round(v) for v in work([BODY_SEED])[0]), 128, thresh=0)
bird = bird.point(lambda v: 255 if v == 128 else 0)

perch = ImageChops.multiply(poly_mask(size, PERCH), not_white)
perch = ImageChops.subtract(fill_holes(perch), bird)

# Flat colours of the logo artwork (sampled from flat interior pixels of logo.png).
RED, INK, CHARCOAL, GRAY, WHITE = (233, 25, 45), (5, 1, 2), (48, 48, 48), (157, 157, 157), (254, 254, 254)
TEAL, MAUVE, ORANGE, GOLD, OLIVE, BLUE = (22, 84, 90), (155, 85, 102), (234, 118, 40), (216, 171, 54), (156, 167, 86), (58, 75, 148)
PUPIL = (2, 62, 68)
PALETTES = {
    "plumage": (RED, INK),
    "feet": (RED, INK, CHARCOAL, GRAY),
    "face": (RED, INK, CHARCOAL, GRAY, WHITE, TEAL, PUPIL, MAUVE, ORANGE, GOLD, OLIVE),
    "perch": (INK, CHARCOAL, GRAY, WHITE, BLUE),
}


def snap(pixel, region):
    """Snap one pixel to its region's palette; None means transparent."""
    r, g, b = pixel[:3]
    if region == "plumage" and max(r, g, b) - min(r, g, b) < 60:
        return INK if r + g + b < 420 else None
    colour = min(PALETTES[region], key=lambda c: (c[0] - r) ** 2 + (c[1] - g) ** 2 + (c[2] - b) ** 2)
    if colour in (ORANGE, GOLD):
        colour = GOLD if g >= 145 else ORANGE
    return colour


regions = Image.new("L", size)
draw = ImageDraw.Draw(regions)
draw.polygon(work(GOGGLES_AND_BIB), fill=2)
draw.polygon(work(LENS), fill=2)
for polygon in FEET:
    draw.polygon(work(polygon), fill=1)
NAMES = {0: "plumage", 1: "feet", 2: "face"}
snapped = Image.new("RGBA", size, (0, 0, 0, 0))
data = []
for pixel, in_bird, in_perch, region in zip(art.get_flattened_data(), bird.get_flattened_data(), perch.get_flattened_data(), regions.get_flattened_data()):
    colour = snap(pixel, NAMES[region]) if in_bird else snap(pixel, "perch") if in_perch else None
    data.append(colour + (255,) if colour else (0, 0, 0, 0))
snapped.putdata(data)
alpha = snapped.getchannel("A")


TRACE = vtracer.Config(
    hierarchical="stacked", mode="spline", filter_speckle=8, color_precision=8,
    layer_difference=8, corner_threshold=60, length_threshold=5.0, splice_threshold=45, path_precision=1,
)


# The head keeps tiny specks: the eye's catchlights are only a few pixels wide.
HEAD_TRACE = vtracer.Config(
    hierarchical="stacked", mode="spline", filter_speckle=2, color_precision=8,
    layer_difference=8, corner_threshold=60, length_threshold=5.0, splice_threshold=45, path_precision=1,
)


def layer(mask, name, config=TRACE):
    image = Image.new("RGBA", size, (0, 0, 0, 0))
    image.paste(snapped, mask=ImageChops.multiply(mask, alpha))
    path = OUT / f".{name}-trace-input.png"
    image.save(path)
    out = OUT / f".{name}-trace-output.svg"
    config.convert_file(str(path), str(out))
    body = out.read_text()
    paths = re.findall(r"<path[^>]*/>", body)
    return "\n".join(paths)


head_mask = ImageChops.multiply(bird, poly_mask(size, HEAD))
tail_region = poly_mask(size, TAIL)
tail_band = ImageChops.multiply(band_mask(size, TAIL_CUT, 60), ImageChops.invert(tail_region))
tail_mask = ImageChops.multiply(bird, ImageChops.lighter(tail_region, tail_band))
torso_mask = ImageChops.subtract(ImageChops.subtract(bird, head_mask), ImageChops.multiply(bird, tail_region))
# The torso keeps the art just past the neck seam, so a small head turn
# reveals the same ink line rather than a gap.
neck_band = ImageChops.multiply(band_mask(size, HEAD_CUT, 160), head_mask)

parts = {
    "cardinal-tail": layer(tail_mask, "tail"),
    "cardinal-torso": layer(ImageChops.lighter(torso_mask, neck_band), "torso"),
    "cardinal-head": layer(head_mask, "head", HEAD_TRACE),
}
perch_paths = layer(perch, "perch")


def logo_to_work(point):
    return work([point])[0]


def fmt(v):
    return f"{v:.1f}".rstrip("0").rstrip(".")


def bird_groups(indent):
    pad = " " * indent
    lines = [f'{pad}<g id="cardinal-body">']
    for gid, paths in parts.items():
        lines.append(f'{pad}  <g id="{gid}">')
        lines.extend(f"{pad}    {p}" for p in paths.splitlines())
        lines.append(f"{pad}  </g>")
    lines.append(f"{pad}</g>")
    return "\n".join(lines)


# ---------- specimen plate ----------
PLATE_W, PLATE_H = 370, 470
bounds = (290, 215, 1440, 1262)
fit = min((PLATE_W - 20) / (bounds[2] - bounds[0]), (PLATE_H - 20) / (bounds[3] - bounds[1]))
s = fit / WORK
tx = (PLATE_W - fit * (bounds[2] - bounds[0])) / 2 - fit * (bounds[0] - CROP[0])
ty = (PLATE_H - fit * (bounds[3] - bounds[1])) / 2 - fit * (bounds[1] - CROP[1])
o = {k: logo_to_work(v) for k, v in PIVOTS.items()}

plate = f"""<svg id="cardinal-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {PLATE_W} {PLATE_H}" width="{PLATE_W}" height="{PLATE_H}" shape-rendering="geometricPrecision" role="img" aria-label="Goggled Wesleyan cardinal perched on a laboratory syringe">
  <style>
    :root {{
      color-scheme: light dark;
    }}
    /* Traced from brand/logo.png by brand/trace-cardinal.py. Pivots are in trace pixels. */
    #cardinal-body,
    #cardinal-tail,
    #cardinal-head {{
      transform-box: view-box;
      transition: transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
    }}

    #cardinal-body {{
      transform-origin: {fmt(o["body"][0])}px {fmt(o["body"][1])}px;
      animation: cardinalBreathe 2.4s ease-in-out infinite;
    }}

    #cardinal-tail {{
      transform-origin: {fmt(o["tail"][0])}px {fmt(o["tail"][1])}px;
      animation: cardinalTailBob 3.0s cubic-bezier(0.25, 1, 0.5, 1) infinite;
    }}

    #cardinal-head {{
      transform-origin: {fmt(o["head"][0])}px {fmt(o["head"][1])}px;
      animation: cardinalHeadSnap 6.0s cubic-bezier(0.2, 0.9, 0.3, 1) infinite;
    }}

    /* Standalone hover */
    #cardinal-svg:hover #cardinal-head {{
      animation: none !important;
      transform: rotate(-3deg);
    }}

    #cardinal-svg:hover #cardinal-tail {{
      animation: none !important;
      transform: rotate(-3deg);
    }}

    #cardinal-svg:hover #cardinal-body {{
      animation: none !important;
      transform: scale(1.012);
    }}

    @keyframes cardinalBreathe {{
      0%, 100% {{
        transform: scale(1);
      }}
      50% {{
        transform: scale(1.012);
      }}
    }}

    @keyframes cardinalTailBob {{
      0%, 100% {{
        transform: rotate(0deg);
      }}
      14% {{
        transform: rotate(-3deg);
      }}
      22% {{
        transform: rotate(2deg);
      }}
      30%, 55% {{
        transform: rotate(0deg);
      }}
      62% {{
        transform: rotate(-2.5deg);
      }}
      70% {{
        transform: rotate(1.5deg);
      }}
      80% {{
        transform: rotate(0deg);
      }}
    }}

    @keyframes cardinalHeadSnap {{
      0%, 18% {{
        transform: rotate(0deg);
      }}
      21%, 42% {{
        transform: rotate(-2.5deg);
      }}
      45%, 68% {{
        transform: rotate(2deg);
      }}
      71%, 86% {{
        transform: rotate(1deg);
      }}
      90%, 100% {{
        transform: rotate(0deg);
      }}
    }}

    @media (prefers-reduced-motion: reduce) {{
      #cardinal-body,
      #cardinal-tail,
      #cardinal-head {{
        animation: none !important;
        transition: none !important;
        transform: none !important;
      }}
    }}
  </style>
  <g id="cardinal-on-syringe" transform="matrix({round(s, 5)} 0 0 {round(s, 5)} {fmt(tx)} {fmt(ty)})">
    <g id="cardinal-perch">
{chr(10).join("      " + p for p in perch_paths.splitlines())}
    </g>
{bird_groups(4)}
  </g>
</svg>
"""
(OUT / "cardinal.svg").write_text(plate)

# ---------- nest scene: mirrored so the bird faces the glassware ----------
FIELD_SCALE = 0.34
fs = FIELD_SCALE / WORK
fx = 505 + FIELD_SCALE * 1365 - FIELD_SCALE * CROP[0]
fy = 495 - FIELD_SCALE * 1100 + FIELD_SCALE * CROP[1]
field = FIELD.read_text()
start = min(i for i in (field.find('  <g id="cardinal"'), field.find('  <g id="cardinal-tail"')) if i >= 0)
end = field.index('  <g id="woven-nest-front"')
bird_markup = (
    f'  <g id="cardinal" data-name="Cardinal" inkscape:label="Cardinal" inkscape:groupmode="layer" '
    f'transform="matrix({-round(fs, 5)} 0 0 {round(fs, 5)} {fmt(fx)} {fmt(fy)})">\n'
    + bird_groups(4) + "\n  </g>\n"
)
(OUT / "cardinal-field.svg").write_text(field[:start] + bird_markup + field[end:])
for name in ("cardinal.svg", "cardinal-field.svg"):
    print(f"wrote {OUT / name} ({(OUT / name).stat().st_size} bytes)")
