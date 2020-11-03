import * as Vibrant from 'node-vibrant';

const image = document.getElementById('main_image');
Vibrant.from(image.getAttribute('src'))
    .getSwatches()
    .then(swatches => {
        for (const swatch in swatches) {
            if (Object.prototype.hasOwnProperty.call(swatches, swatch) && swatches[swatch]) {
                const badge = document.createElement('span');
                const labelNode = document.createTextNode(swatch + ' / ' + swatches[swatch].getHex());
                const referenceNode = document.getElementById('swatcheslist');
                badge.className = 'badge badge-secondary my-2 py-2';
                badge.setAttribute(
                    'style',
                    'display: block; text-shadow: 1px 1px 3px #000; background-color: ' + swatches[swatch].getHex(),
                );
                badge.appendChild(labelNode);
                referenceNode.append(badge);
            }
        }
    });
