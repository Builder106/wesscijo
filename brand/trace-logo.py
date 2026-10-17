"""Trace the supplied logo into editable, named SVG artwork.

Run on the build host with: uv run --with vtracer==1.0.0a4 --with pillow python brand/trace-logo.py
"""

from pathlib import Path
from xml.etree import ElementTree as ET

from PIL import Image, ImageDraw, ImageFilter
import vtracer


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "brand/logo.png"
OUTPUT = ROOT / "brand/logo-semantic.svg"
UPPER_INPUT = ROOT / "preview/assets/.upper-lettering-trace-input.png"
UPPER_OUTPUT = ROOT / "preview/assets/.upper-lettering-trace-output.svg"
LOWER_INPUT = ROOT / "preview/assets/.lower-lettering-trace-input.png"
LOWER_OUTPUT = ROOT / "preview/assets/.lower-lettering-trace-output.svg"
TRACE_INPUT = ROOT / "preview/assets/.cardinal-trace-input.png"
TRACE_OUTPUT = ROOT / "preview/assets/.cardinal-trace-output.svg"
FEET_INPUT = ROOT / "preview/assets/.feet-trace-input.png"
FEET_OUTPUT = ROOT / "preview/assets/.feet-trace-output.svg"
SVG_NS = "http://www.w3.org/2000/svg"
ET.register_namespace("", SVG_NS)


def element(parent, tag, **attributes):
    return ET.SubElement(parent, f"{{{SVG_NS}}}{tag}", attributes)


source = Image.open(SOURCE).convert("RGBA")
bounds = source.getchannel("A").point(lambda value: 255 if value > 8 else 0).getbbox()
source = source.crop(bounds)
source.thumbnail((768, 768), Image.Resampling.LANCZOS)
canvas = Image.new("RGBA", (768, 768), (0, 0, 0, 0))
canvas.alpha_composite(source, ((768 - source.width) // 2, (768 - source.height) // 2))

red_region = Image.new("L", canvas.size)
red_pixels = red_region.load()
pixels = canvas.load()
for y in range(768):
    for x in range(768):
        red, green, blue, alpha = pixels[x, y]
        if alpha and red > 120 and red > green * 1.45 and red > blue * 1.3:
            red_pixels[x, y] = 255
region = red_region.filter(ImageFilter.MaxFilter(25))
draw = ImageDraw.Draw(region)
draw.polygon([(423, 166), (553, 168), (614, 226), (610, 307), (553, 333), (430, 282)], fill=255)
draw.polygon([(455, 270), (560, 276), (579, 348), (493, 359), (470, 333)], fill=255)

feet_region = Image.new("L", canvas.size)
feet_draw = ImageDraw.Draw(feet_region)
feet_draw.polygon([(346, 510), (369, 512), (398, 541), (417, 555), (438, 578), (442, 596), (432, 613), (408, 613), (389, 587), (370, 551)], fill=255)
feet_draw.polygon([(420, 510), (449, 514), (478, 535), (502, 539), (517, 558), (517, 579), (506, 587), (486, 572), (465, 551), (439, 536)], fill=255)
feet_draw.ellipse((356, 536, 392, 570), fill=255)
feet_draw.ellipse((466, 563, 500, 598), fill=255)

syringe_region = Image.new("L", canvas.size)
syringe_draw = ImageDraw.Draw(syringe_region)
syringe_draw.polygon([(294, 618), (310, 600), (512, 522), (548, 542), (330, 653)], fill=255)
syringe_draw.polygon([(475, 541), (638, 482), (645, 495), (484, 562)], fill=255)

layers = {
    "upper": Image.new("RGBA", canvas.size),
    "lower": Image.new("RGBA", canvas.size),
    "syringe": Image.new("RGBA", canvas.size),
    "cardinal": Image.new("RGBA", canvas.size),
    "feet": Image.new("RGBA", canvas.size),
}
layer_pixels = {name: image.load() for name, image in layers.items()}
region_pixels = region.load()
feet_mask = feet_region.load()
syringe_mask = syringe_region.load()
for y in range(768):
    for x in range(768):
        red, green, blue, alpha = pixels[x, y]
        if not alpha:
            continue
        if feet_mask[x, y] and max(red, green, blue) < 125:
            layer = "feet"
        elif region_pixels[x, y] and min(red, green, blue) < 245:
            layer = "cardinal"
        elif syringe_mask[x, y]:
            layer = "syringe"
        elif max(red, green, blue) < 160 and 286 ** 2 < (x - 384) ** 2 + (y - 384) ** 2 < 372 ** 2:
            layer = "upper" if y < 384 else "lower"
        else:
            continue
        layer_pixels[layer][x, y] = (red, green, blue, alpha)

for name, source_path, output_path in (
    ("upper", UPPER_INPUT, UPPER_OUTPUT),
    ("lower", LOWER_INPUT, LOWER_OUTPUT),
    ("cardinal", TRACE_INPUT, TRACE_OUTPUT),
    ("feet", FEET_INPUT, FEET_OUTPUT),
):
    layers[name].save(source_path)
    vtracer.convert_file(str(source_path), str(output_path))

def append_trace(parent, path):
    for child in ET.parse(path).getroot():
        parent.append(child)


svg = ET.Element(f"{{{SVG_NS}}}svg", {"viewBox": "0 0 768 768", "role": "img", "aria-labelledby": "logo-title logo-description"})
element(svg, "title", id="logo-title").text = "The Wesleyan Science Journal"
element(svg, "desc", id="logo-description").text = "The original seal artwork with a goggled red cardinal landing on a laboratory syringe."
seal = element(svg, "g", id="journal-seal")
element(seal, "circle", id="seal-paper", cx="384", cy="384", r="376", fill="#ffffff")
element(seal, "circle", id="seal-outer-rule", cx="384", cy="384", r="375.5", fill="none", stroke="#252525", **{"stroke-width": "1.2"})
element(seal, "circle", id="seal-inner-rule", cx="388", cy="384", r="278", fill="none", stroke="#858585", **{"stroke-width": "1.2"})
append_trace(element(seal, "g", id="seal-lettering-upper"), UPPER_OUTPUT)
append_trace(element(seal, "g", id="seal-lettering-lower"), LOWER_OUTPUT)
syringe = element(svg, "g", id="syringe")
needle = element(syringe, "g", id="syringe-needle", fill="none", **{"stroke-linecap": "round"})
element(needle, "path", d="M 477,565 578,520", stroke="#284999", **{"stroke-width": "6"})
element(needle, "path", d="M 578,520 635,491", stroke="#6b6b6b", **{"stroke-width": "1.5"})
barrel = element(syringe, "g", id="syringe-barrel", **{"stroke-linecap": "round", "stroke-linejoin": "round"})
element(barrel, "path", d="M 328,615 471,551 Q 479,548 484,558 L 490,568 340,638 329,630 Z", fill="#f7f7f6", stroke="#252525", **{"stroke-width": "2.2"})
element(barrel, "path", d="M 335,618 477,557 M 340,629 484,565", fill="none", stroke="#717171", **{"stroke-width": "1.5"})
element(barrel, "path", d="M 346,631 C 344,648 352,660 365,655", fill="none", stroke="#252525", **{"stroke-width": "2"})
hub = element(syringe, "g", id="syringe-hub", **{"stroke-linejoin": "round"})
element(hub, "path", d="M 328,609 336,606 340,628 330,631 Z", fill="#284999", stroke="#252525", **{"stroke-width": "1.5"})
element(hub, "path", d="M 312,633 325,627 329,642 315,646 Z", fill="#284999", stroke="#252525", **{"stroke-width": "1.5"})

bird = element(svg, "g", id="cardinal")
wing_defs = element(bird, "defs")
wing_clip = element(wing_defs, "clipPath", id="flight-wing-cutout", clipPathUnits="userSpaceOnUse")
element(wing_clip, "path", d="M 153,499 C 185,450 233,404 292,371 Q 351,341 419,345 L 421,424 Q 352,472 192,515 Z")
append_trace(element(bird, "g", id="cardinal-body"), TRACE_OUTPUT)
element(bird, "path", id="cardinal-resting-wing-cover", opacity="0", fill="#E81B2E", d="M 153,499 C 185,450 233,404 292,371 Q 351,341 419,345 L 421,424 Q 352,472 192,515 Z")
append_trace(element(bird, "g", id="cardinal-landing-feet"), FEET_OUTPUT)

wing = element(bird, "g", id="cardinal-flight-wing", opacity="0")
element(wing, "use", href="#cardinal-body", **{"clip-path": "url(#flight-wing-cutout)"})

ET.ElementTree(svg).write(OUTPUT, encoding="utf-8", xml_declaration=True)
print(f"wrote {OUTPUT} ({OUTPUT.stat().st_size} bytes)")
