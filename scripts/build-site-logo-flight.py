"""Build the site-paced light and dark flights from the approved preview SVG."""

from pathlib import Path
import xml.etree.ElementTree as ET


ROOT = Path(__file__).resolve().parents[1]
SVG_NS = "http://www.w3.org/2000/svg"
ET.register_namespace("", SVG_NS)

source = (ROOT / "preview/assets/logo-flight.svg").read_text(encoding="utf-8")
if source.count('dur="1800ms"') != 7:
    raise ValueError("Expected seven 1800ms flight channels in the approved SVG")

flight = source.replace('dur="1800ms"', 'dur="2800ms"')
for theme in ("wesscijo", "wessci-fallback"):
    (ROOT / "themes" / theme / "assets/logo-flight.svg").write_text(
        flight, encoding="utf-8"
    )

tree = ET.ElementTree(ET.fromstring(flight))
seal = tree.find(f".//{{{SVG_NS}}}g[@id='journal-seal']")
if seal is None:
    raise ValueError("Animated logo has no journal seal")

for element in seal:
    name = element.get("id")
    if name == "seal-paper":
        element.set("fill", "none")
    elif name in ("seal-outer-rule", "seal-inner-rule"):
        element.set("stroke", "#f5f5f5")
    elif name in ("seal-lettering-upper", "seal-lettering-lower"):
        for letter in element.iter(f"{{{SVG_NS}}}path"):
            letter.set("fill", "#f5f5f5")

for theme in ("wesscijo", "wessci-fallback"):
    tree.write(
        ROOT / "themes" / theme / "assets/logo-flight-dark.svg",
        encoding="utf-8",
        xml_declaration=True,
    )
