from __future__ import annotations

from pathlib import Path
from PIL import Image, ImageDraw, ImageFont
import xml.etree.ElementTree as ET


ROOT = Path(__file__).resolve().parent.parent
DIAGRAM_DIR = ROOT / 'diagram'
OUTPUT_DIR = ROOT / 'output_diagram'

NS = {
    'g': 'http://graphml.graphdrawing.org/xmlns',
    'y': 'http://www.yworks.com/xml/graphml',
}


def font(size: int):
    for candidate in [
        'C:/Windows/Fonts/arial.ttf',
        'C:/Windows/Fonts/Arial.ttf',
    ]:
        if Path(candidate).exists():
            return ImageFont.truetype(candidate, size)
    return ImageFont.load_default()


FONT_12 = font(12)
FONT_11 = font(11)


def parse_graphml(path: Path):
    tree = ET.parse(path)
    root = tree.getroot()
    nodes = []
    edges = []
    for node in root.findall('.//g:node', NS):
        node_id = node.attrib['id']
        geom = node.find('.//y:Geometry', NS)
        label = node.find('.//y:NodeLabel', NS)
        shape = node.find('.//y:Shape', NS)
        fill = node.find('.//y:Fill', NS)
        border = node.find('.//y:BorderStyle', NS)
        nodes.append({
            'id': node_id,
            'x': float(geom.attrib.get('x', 0)),
            'y': float(geom.attrib.get('y', 0)),
            'w': float(geom.attrib.get('width', 100)),
            'h': float(geom.attrib.get('height', 50)),
            'label': label.text if label is not None and label.text else '',
            'shape': shape.attrib.get('type', 'rectangle') if shape is not None else 'rectangle',
            'fill': fill.attrib.get('color', '#FFFFFF') if fill is not None else '#FFFFFF',
            'border': border.attrib.get('color', '#111111') if border is not None else '#111111',
        })
    for edge in root.findall('.//g:edge', NS):
        label = edge.find('.//y:EdgeLabel', NS)
        edges.append({
            'source': edge.attrib['source'],
            'target': edge.attrib['target'],
            'label': label.text if label is not None and label.text else '',
        })
    return nodes, edges


def wrap_text(draw: ImageDraw.ImageDraw, text: str, width: int, used_font):
    lines = []
    for paragraph in text.split('\n'):
        words = paragraph.split()
        if not words:
            lines.append('')
            continue
        current = words[0]
        for word in words[1:]:
            trial = current + ' ' + word
            bbox = draw.multiline_textbbox((0, 0), trial, font=used_font)
            if bbox[2] - bbox[0] <= width:
                current = trial
            else:
                lines.append(current)
                current = word
        lines.append(current)
    return lines


def draw_node(draw: ImageDraw.ImageDraw, node: dict):
    x1, y1 = node['x'], node['y']
    x2, y2 = x1 + node['w'], y1 + node['h']
    if node['shape'] == 'ellipse':
        draw.ellipse([x1, y1, x2, y2], fill=node['fill'], outline=node['border'], width=2)
    elif node['shape'] == 'diamond':
        cx, cy = (x1 + x2) / 2, (y1 + y2) / 2
        points = [(cx, y1), (x2, cy), (cx, y2), (x1, cy)]
        draw.polygon(points, fill=node['fill'], outline=node['border'])
    else:
        draw.rounded_rectangle([x1, y1, x2, y2], radius=10, fill=node['fill'], outline=node['border'], width=2)

    lines = wrap_text(draw, node['label'], int(node['w']) - 16, FONT_12)
    text = '\n'.join(lines)
    bbox = draw.multiline_textbbox((0, 0), text, font=FONT_12, align='center')
    tw = bbox[2] - bbox[0]
    th = bbox[3] - bbox[1]
    tx = x1 + (node['w'] - tw) / 2
    ty = y1 + (node['h'] - th) / 2
    draw.multiline_text((tx, ty), text, font=FONT_12, fill='#111111', align='center')


def draw_edge(draw: ImageDraw.ImageDraw, source: dict, target: dict, label: str):
    sx = source['x'] + source['w'] / 2
    sy = source['y'] + source['h'] / 2
    tx = target['x'] + target['w'] / 2
    ty = target['y'] + target['h'] / 2
    draw.line([sx, sy, tx, ty], fill='#4B5563', width=2)
    mx, my = (sx + tx) / 2, (sy + ty) / 2
    arrow = 8
    if tx >= sx:
        points = [(tx, ty), (tx - arrow, ty - arrow / 2), (tx - arrow, ty + arrow / 2)]
    else:
        points = [(tx, ty), (tx + arrow, ty - arrow / 2), (tx + arrow, ty + arrow / 2)]
    draw.polygon(points, fill='#4B5563')
    lines = wrap_text(draw, label, 180, FONT_11)
    text = '\n'.join(lines)
    bbox = draw.multiline_textbbox((0, 0), text, font=FONT_11, align='center')
    pad = 4
    draw.rounded_rectangle([mx - (bbox[2] - bbox[0]) / 2 - pad, my - (bbox[3] - bbox[1]) / 2 - pad, mx + (bbox[2] - bbox[0]) / 2 + pad, my + (bbox[3] - bbox[1]) / 2 + pad], radius=6, fill='#FFFFFF', outline='#D1D5DB')
    draw.multiline_text((mx - (bbox[2] - bbox[0]) / 2, my - (bbox[3] - bbox[1]) / 2), text, font=FONT_11, fill='#111111', align='center')


def render_graphml(path: Path):
    nodes, edges = parse_graphml(path)
    if not nodes:
        return None
    max_x = max(n['x'] + n['w'] for n in nodes) + 80
    max_y = max(n['y'] + n['h'] for n in nodes) + 80
    image = Image.new('RGB', (int(max_x), int(max_y)), 'white')
    draw = ImageDraw.Draw(image)
    node_map = {n['id']: n for n in nodes}
    for edge in edges:
        if edge['source'] in node_map and edge['target'] in node_map:
            draw_edge(draw, node_map[edge['source']], node_map[edge['target']], edge['label'])
    for node in nodes:
        draw_node(draw, node)
    return image


def main():
    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    for graphml in DIAGRAM_DIR.glob('*.graphml'):
        image = render_graphml(graphml)
        if image is None:
            continue
        out = DIAGRAM_DIR / f'{graphml.stem}.png'
        image.save(out)
        image.save(OUTPUT_DIR / out.name)
    print('PNG exports generated.')


if __name__ == '__main__':
    main()
