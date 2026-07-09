import { Vibrant } from 'node-vibrant/browser';

const image = document.getElementById('main_image');
if (image) {
    Vibrant.from(image)
        .getPalette()
        .then(swatches => {
            for (const swatch in swatches) {
                if (Object.prototype.hasOwnProperty.call(swatches, swatch) && swatches[swatch]) {
                    const badge = document.createElement('span');
                    const labelNode = document.createTextNode(swatch + ' / ' + swatches[swatch].hex);
                    const referenceNode = document.getElementById('swatcheslist');
                    badge.className = 'badge badge-secondary my-2 py-2';
                    badge.setAttribute(
                        'style',
                        'display: block; text-shadow: 1px 1px 3px #000; background-color: ' +
                            swatches[swatch].hex +
                            '; color: ' +
                            swatches[swatch].titleTextColor,
                    );
                    badge.appendChild(labelNode);
                    referenceNode.append(badge);
                }
            }
        })
        .catch(err => {
            console.warn('Vibrant failed to extract colors:', err);
        });
}
