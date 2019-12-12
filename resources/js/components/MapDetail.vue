<template>
    <l-map
            class="w-full h-full rounded z-10"
            ref="map"
            :zoom="zoom"
            :center="center"
            :bounds="bounds"
            :options="mapOptions"
    >
        <l-tile-layer
                v-for="tileProvider in tileProviders"
                layerType="base"
                :name="tileProvider.name"
                :visible="tileProvider.visible"
                :url="tileProvider.url"
                :attribution="tileProvider.attribution"
                :key="tileProvider.name"
        />

        <l-geo-json
                ref="geojsonLayer"
                :geojson="geojson"
                :options="options"
        ></l-geo-json>

    </l-map>
</template>

<script>
    import {LMap, LTileLayer, LGeoJson} from 'vue2-leaflet'

    export default {
        props: {
            colors: [],
            type: String,
            value: {
                default: null
            },
            edit: {
                type: Boolean,
                default: false
            },
            latitude_field: String,
            longitude_field: String,
            geojson_field: String,
            zoom: Number,
        },
        components: {
            LMap,
            LTileLayer,
            LGeoJson,
        },
        data: function() {
            return  {
                colorIndex: 0,
                map: null,
                geojsonLayer: null,
                center: [0, 0],
                bounds: null,
                tileProviders: [{
                    name: 'OpenStreetMap',
                    visible: true,
                    attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
                    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
                }],
                options: {
                    style: function () {
                        return {
                            weight: 2,
                            color: 'blue',
                            opacity: 0.8,
                            fillColor: 'blue',
                            fillOpacity: 0.3
                        }
                    },
                    pointToLayer: (feature, latlng) => {
                        const color = this.colors[this.colorIndex];

                        let icon = L.icon({
                            iconUrl: color.icon,
                            iconSize: [25, 41],
                            iconAnchor: [13, 40],
                        });


                        this.colorIndex++;
                        if(this.colorIndex >= this.colors.length) {
                            this.colorIndex = 0;
                        }

                        return L.marker(latlng).setIcon(icon).bindTooltip(color.title, {
                            permanent: false,
                            direction: 'right'
                        });
                    }
                },
                mapOptions: {},
            }
        },
        computed: {
            geojson() {
                if (this.type == "LatLon" || this.type == "LatLonField") {
                    return {
                        type: 'Point',
                        coordinates: [
                            this.value.lon,
                            this.value.lat,
                        ]
                    }
                } else if (this.type == "GeoJSON") {
                    return JSON.parse(this.value)
                } else {
                    return this.value
                }
            }
        },
        mounted() {
            this.$nextTick(() => {
                this.map = this.$refs.map.mapObject

                this.geojsonLayer = this.$refs.geojsonLayer.mapObject

                this.setCenter()

            })

        },
        methods: {
            getColors() {
                return this.colors;
            },
            setCenter() {
                if (this.geojson !== null) {
                    this.map.fitBounds(L.geoJSON(this.geojson).getBounds(), {maxZoom: 18}).setZoom(this.zoom)
                }
            },
        }
    }
</script>

<style scoped>
    @import "../../../node_modules/leaflet/dist/leaflet.css"
</style>
