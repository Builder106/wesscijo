"""Build the small perched cardinal from a low-scale trace of the logo bird.

Run on the build host:
uv run --with vtracer==1.0.0a4 --with pillow python brand/trace-cardinal.py OUT_DIR 0.4
python3 brand/build-editorial-cardinals.py OUT_DIR/cardinal.svg

Writes cardinal-perch.svg to both theme asset folders. The pages inline it, so
assets/cardinal-watch.js can turn the head and tail; the file has no animation
of its own.
The toes grip a line 21% above the bottom of the viewBox. style.css shifts
the bird down by that much, so the section rule becomes its perch.
"""

import re
import sys
from pathlib import Path
import xml.etree.ElementTree as ET


ROOT = Path(__file__).resolve().parents[1]
SVG = "http://www.w3.org/2000/svg"
ET.register_namespace("", SVG)

# Plate units from trace-cardinal.py. The toes grip the syringe at y = 333.
VIEW_BOX = "4 68 360 336"

# The logo's flat colours, as sampled in trace-cardinal.py. vtracer averages
# edge pixels into near-duplicates; snapping them back keeps the file small.
PALETTE = {
    "#e9192d": (233, 25, 45), "#050102": (5, 1, 2), "#303030": (48, 48, 48),
    "#9d9d9d": (157, 157, 157), "#fefefe": (254, 254, 254), "#16545a": (22, 84, 90),
    "#023e44": (2, 62, 68), "#9b5566": (155, 85, 102), "#ea7628": (234, 118, 40),
    "#d8ab36": (216, 171, 54), "#9ca756": (156, 167, 86),
}


def tag(name):
    return f"{{{SVG}}}{name}"


def snap(colour):
    rgb = tuple(int(colour[i:i + 2], 16) for i in (1, 3, 5))
    return min(PALETTE, key=lambda name: sum((a - b) ** 2 for a, b in zip(PALETTE[name], rgb)))


def by_id(root, element_id):
    return next(element for element in root.iter() if element.get("id") == element_id)


def pivots(source_text):
    """Head and tail pivots from the plate's stylesheet, in trace pixels."""
    found = {}
    for part in ("tail", "head"):
        match = re.search(rf"#cardinal-{part} \{{[^}}]*?transform-origin: ([\d.]+px [\d.]+px)", source_text)
        found[part] = match.group(1)
    return found


def part(group, name, origin=None):
    """Copy one traced part, grouping consecutive paths that share a colour."""
    out = ET.Element(tag("g"), {"class": f"cardinal__{name}"})
    if origin:
        out.set("style", f"transform-origin: {origin}")
    run = None
    for path in group.iter(tag("path")):
        colour = snap(path.get("fill"))
        if run is None or run.get("fill") != colour:
            run = ET.SubElement(out, tag("g"), {"fill": colour})
        attributes = {"d": path.get("d")}
        if path.get("transform"):
            attributes["transform"] = path.get("transform")
        ET.SubElement(run, tag("path"), attributes)
    return out


def perched_cardinal(source_path):
    text = Path(source_path).read_text()
    source = ET.fromstring(text)
    origin = pivots(text)
    root = ET.Element(tag("svg"), {
        "class": "cardinal", "viewBox": VIEW_BOX, "aria-hidden": "true", "focusable": "false",
    })
    plate = ET.SubElement(root, tag("g"), {"transform": by_id(source, "cardinal-on-syringe").get("transform")})
    plate.append(part(by_id(source, "cardinal-tail"), "tail", origin["tail"]))
    plate.append(part(by_id(source, "cardinal-torso"), "torso"))
    plate.append(part(by_id(source, "cardinal-head"), "head", origin["head"]))
    return root


if __name__ == "__main__":
    data = ET.tostring(perched_cardinal(sys.argv[1]), encoding="unicode") + "\n"
    for theme in ("wesscijo", "wessci-fallback"):
        (ROOT / "themes" / theme / "assets" / "cardinal-perch.svg").write_text(data)
