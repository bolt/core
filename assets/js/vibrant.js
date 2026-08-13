import { Vibrant } from 'node-vibrant/browser';

const image = document.getElementById('main_image');
Vibrant.from(image.getAttribute('src'))
    .getPalette()
    .then(palettes => {
        for (const palette in palettes) {
            if (!(Object.prototype.hasOwnProperty.call(palettes, palette) && palettes[palette])) {
                continue;
            }

            const badge = document.createElement('span');
            const labelNode = document.createTextNode(palette + ' / ' + palettes[palette].hex);
            const referenceNode = document.getElementById('swatcheslist');
            badge.className = 'badge badge-secondary my-2 py-2';
            badge.setAttribute(
                'style',
                'display: block; text-shadow: 1px 1px 3px #000; background-color: ' + palettes[palette].hex,
            );
            badge.appendChild(labelNode);
            referenceNode.append(badge);
        }
    });
