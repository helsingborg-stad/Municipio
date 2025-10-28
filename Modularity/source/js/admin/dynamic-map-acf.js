export default class DynamicMapAcf {

    constructor() {
        this.init();
    }

    init() {
        let startPosition;
        
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof acf == 'undefined') return;

            acf.addAction('google_map_init', (map, marker, field) => {
                if (this.startPositionFieldCondition(map, marker, field)) {
                    startPosition = marker.position;
                } else {
                  this.setMapPosition(startPosition, map, marker);
                }
            });

            $('[data-key="field_64c77830c32aa"] .acf-input').change((e) => {
                const input = document.querySelector('[name="acf[field_64c77830c32aa]"');
                if (input) {
                    let json;
                    try {
                        json = JSON.parse(input.value);
                        startPosition = {lat: json.lat, lng: json.lng};
                    } catch (e) {
                        return console.error("Couldn't parse JSON");
                    }
                };
            });
        });
    }

    startPositionFieldCondition(map, marker, field) {
        return field.data.key === "field_64c77830c32aa" && marker && marker.position && map;
    }

    setMapPosition(startPosition, map, marker) {
        if (!map || marker.position) return;

        if (startPosition) {
            map.setCenter(startPosition);
        }
    }
}

new DynamicMapAcf();