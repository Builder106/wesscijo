"""Trace the supplied seal's cardinal into editable, named SVG artwork.

Run on the build host with: uv run --with vtracer==1.0.0a4 --with pillow python brand/trace-logo.py
"""

from pathlib import Path
from xml.etree import ElementTree as ET

from PIL import Image, ImageDraw, ImageFilter
import vtracer


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / "brand/logo.png"
OUTPUT = ROOT / "brand/logo-semantic.svg"
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

region_pixels = region.load()
cardinal = Image.new("RGBA", canvas.size)
out = cardinal.load()
for y in range(768):
    for x in range(768):
        red, green, blue, alpha = pixels[x, y]
        if region_pixels[x, y] and alpha and min(red, green, blue) < 245:
            out[x, y] = (red, green, blue, alpha)

cardinal.save(TRACE_INPUT)
vtracer.convert_file(str(TRACE_INPUT), str(TRACE_OUTPUT))

feet_region = Image.new("L", canvas.size)
feet_draw = ImageDraw.Draw(feet_region)
feet_draw.polygon([(346, 510), (369, 512), (398, 541), (417, 555), (438, 578), (442, 596), (432, 613), (408, 613), (389, 587), (370, 551)], fill=255)
feet_draw.polygon([(420, 510), (449, 514), (478, 535), (502, 539), (517, 558), (517, 579), (506, 587), (486, 572), (465, 551), (439, 536)], fill=255)
feet_mask = feet_region.load()
feet_image = Image.new("RGBA", canvas.size)
feet_pixels = feet_image.load()
for y in range(768):
    for x in range(768):
        red, green, blue, alpha = pixels[x, y]
        if feet_mask[x, y] and alpha and max(red, green, blue) < 125:
            feet_pixels[x, y] = (red, green, blue, alpha)
feet_image.save(FEET_INPUT)
vtracer.convert_file(str(FEET_INPUT), str(FEET_OUTPUT))

svg = ET.Element(f"{{{SVG_NS}}}svg", {"viewBox": "0 0 768 768", "role": "img", "aria-labelledby": "logo-title logo-description"})
element(svg, "title", id="logo-title").text = "The Wesleyan Science Journal"
element(svg, "desc", id="logo-description").text = "A goggled red cardinal lands on a laboratory syringe inside the journal seal."
defs = element(svg, "defs")
element(defs, "path", id="seal-lettering-upper-arc", d="M 86,385 A 298,298 0 0 1 682,385")
element(defs, "path", id="seal-lettering-lower-arc", d="M 48,383 A 336,336 0 0 0 720,383")

seal = element(svg, "g", id="journal-seal")
element(seal, "circle", id="seal-paper", cx="384", cy="384", r="376", fill="#ffffff", stroke="#171414", **{"stroke-width": "2"})
element(seal, "circle", id="seal-inner-rule", cx="384", cy="384", r="280", fill="none", stroke="#747474", **{"stroke-width": "1.5"})
upper = element(seal, "text", id="seal-lettering-upper", fill="#171414", **{"font-family": "Georgia, Times New Roman, serif", "font-size": "68", "letter-spacing": "16", "text-anchor": "middle"})
element(upper, "textPath", href="#seal-lettering-upper-arc", startOffset="50%").text = "WESLEYAN"
lower = element(seal, "text", id="seal-lettering-lower", fill="#171414", **{"font-family": "Georgia, Times New Roman, serif", "font-size": "48", "letter-spacing": "5", "text-anchor": "middle"})
element(lower, "textPath", href="#seal-lettering-lower-arc", startOffset="50%").text = "SCIENCE JOURNAL"

syringe = element(svg, "g", id="syringe", stroke="#171414", **{"stroke-linejoin": "round", "stroke-linecap": "round"})
element(syringe, "path", id="syringe-needle", d="M 584,513 636,490", fill="none", stroke="#6f7378", **{"stroke-width": "2"})
element(syringe, "path", id="syringe-needle-hub", d="M 495,553 587,512", fill="none", stroke="#234b9c", **{"stroke-width": "8"})
element(syringe, "path", id="syringe-barrel", d="M 328,610 470,547 489,562 342,632 Z", fill="#f8f8f6", **{"stroke-width": "4"})
element(syringe, "path", id="syringe-barrel-shading", d="M 341,618 479,557 485,562 343,629 Z", fill="#b6b8b8", stroke="none")
element(syringe, "path", id="syringe-barrel-rule", d="M 357,610 477,557", fill="none", **{"stroke-width": "2"})
element(syringe, "path", id="syringe-plunger", d="M 311,626 397,589", fill="none", stroke="#224b9b", **{"stroke-width": "10"})
element(syringe, "path", id="syringe-plunger-cap", d="M 306,616 318,610 326,636 313,642 Z", fill="#254d9a", **{"stroke-width": "3"})
element(syringe, "path", id="syringe-flange", d="M 328,603 332,633 Q 339,642 355,638", fill="none", **{"stroke-width": "3"})

bird = element(svg, "g", id="cardinal")
body = element(bird, "g", id="cardinal-body")
traced = ET.parse(TRACE_OUTPUT).getroot()
for path in traced:
    if path.tag.endswith("path"):
        body.append(path)

feet = element(bird, "g", id="cardinal-landing-feet")
for path in ET.parse(FEET_OUTPUT).getroot():
    if path.tag.endswith("path"):
        feet.append(path)

wing = element(bird, "g", id="cardinal-flight-wing", opacity="0", stroke="#171414", **{"stroke-linejoin": "round", "stroke-linecap": "round"})
element(wing, "path", id="flight-wing-silhouette", d="M 273,372 C 308,341 366,321 413,311 C 403,377 377,414 332,436 C 283,451 226,446 182,430 C 223,416 255,392 273,372 Z", fill="#e81b2e", **{"stroke-width": "3.5"})
element(wing, "path", id="flight-wing-feather-upper", d="M 211,426 C 279,419 343,380 397,331", fill="none", **{"stroke-width": "3"})
element(wing, "path", id="flight-wing-feather-lower", d="M 229,437 C 297,435 357,399 400,364", fill="none", **{"stroke-width": "3"})

ET.ElementTree(svg).write(OUTPUT, encoding="utf-8", xml_declaration=True)
print(f"wrote {OUTPUT} ({OUTPUT.stat().st_size} bytes, {len(bird)} cardinal paths)")
