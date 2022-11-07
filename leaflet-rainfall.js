   
    
    
L.Control.Rainfall = L.Control.extend({
rainOverlays:[],
timeOverlayIndex:0,
isPaused:false,
timeoutId:undefined,

    options:{
        data:[],
        position:'topright',
        transitionMs:750,
        zIndex:200,
        opacity:0
    },

    onAdd:function(map) {
        this.map = map;
        this.container = L.DomUtil.create('div', 'leaflet-rainfall');

        L.DomEvent.disableClickPropagation(this.container);
        L.DomEvent.on(this.container, `control_container`, function (e) {
            L.DomEvent.stopPropagation(e);
        });

        L.DomEvent.disableScrollPropagation(this.container);

        // checkbox div
        let checkbox_div = L.DomUtil.create(
            'div',
            'leaflet-rainfall-toggle',
            this.container
        );
        
        this.checkbox = document.createElement('input');
        this.checkbox.id = 'rainfall-checkbox';
        this.checkbox.type = `checkbox`;
        this.checkbox.checked = false;
        this.checkbox.onclick = () => this.toggle();
       
        checkbox_div.appendChild(this.checkbox);

        let checkbox_label = document.createElement(`span`);
        checkbox_label.innerText = `SIRAD dBZ`;

        checkbox_div.appendChild(checkbox_label);

        // slider
        let slider_div = L.DomUtil.create(
            'div',
            'leaflet-rainfall-slider',
            this.container
        );

        this.slider = document.createElement('input');
        this.slider.id = 'time-slider';
        this.slider.type = 'range';
        this.slider.min = 0;
        this.slider.max = `${this.rainOverlays.length - 1}`;

        slider_div.appendChild(this.slider);

        // let slider_label = document.createElement(`span`);
        // slider_label.innerText = `Rainfall`;

        // slider_div.appendChild(slider_label);
        
        this.timestamp_div = L.DomUtil.create(
            `div`,
            `leaflet-rainfall-timestamp`,
            this.container
        );
        
        this.setDisabled(true);
        this.isPaused = true;

        return this.container;
    },

    onRemove:function() {
        L.DomUtil.remove(this._container);
    },

    setDisabled: function (disabled) {
        this.slider.disabled = disabled;
        this.timestamp_div.innerText = ``;
    },

    toggle:function() {

        if(!this.checkbox.checked) {
            this.setDisabled(true);
            this.removeLayers();
            return;
        }

        this.setDisabled(false);
        this.rainOverlays = this.generateLayers();
        this.addLayers(this.rainOverlays);

        this.slider.max = `${this.rainOverlays.length - 1}`;
        this.timeOverlayIndex = 0;

        this.isPaused = false;

        this.slider.oninput = () => {
            this.hideLayerByIndex(this.timeOverlayIndex);
            this.timeOverlayIndex = this.slider.value;
            this.showLayerByIndex(this.timeOverlayIndex);
            
            this.isPaused = false;
        }

        
        this.setTransitionTimer();
    },
    hideLayerByIndex:function(index) {
        this.rainOverlays[index].layer.setOpacity(0);
        this.timestamp_div.innerHTML = ``;
    },
    showLayerByIndex:function(index){
        this.rainOverlays[index].layer.setOpacity(
            this.options.opacity
        );

        this.timestamp_div.innerHTML = this.rainOverlays[index].timestamp;
    },
    setTransitionTimer:function() {
        this.timeoutId = setTimeout((e) => {
            if(this.isPaused){
                return;
            }
            
            this.rainOverlays.forEach(imageOverlay => {
                imageOverlay.layer.setOpacity(0);
                imageOverlay.layer.addTo(this.map);
            });

            // if(this.checkbox.checked) {
                this.hideLayerByIndex(this.timeOverlayIndex);
                this.incrementLayerIndex();
                this.showLayerByIndex(this.timeOverlayIndex);

                this.slider.value = `${this.timeOverlayIndex}`;

                // recursive method
                this.setTransitionTimer();
            // }

        }, this.options.transitionMs);
    },

    incrementLayerIndex:function() {
        this.timeOverlayIndex++;
        if(this.timeOverlayIndex > this.rainOverlays.length - 1) {
            this.timeOverlayIndex = 0;
        }
    },

    addLayers:function() {
        this.rainOverlays.forEach(imageOverlay => {
            imageOverlay.layer.setOpacity(0);
            imageOverlay.layer.addTo(this.map);
        });
    },

    setData:function(data) {
        console.log('seting data');
        this.options.data = data;
    },

    runAnimation:function() {
        console.log("run Animation");
        this.toggle();
    },

    refreshAnimation:function() {
        clearTimeout(this.timeoutId);
        
        this.removeLayers();
        this.toggle();
    },
    removeLayers:function() {
        this.rainOverlays.forEach(imageOverlay => {
            imageOverlay.layer.removeFrom(this.map);
        });

        this.rainOverlays = [];
        this.timeOverlayIndex = 0;
    },

    generateLayers:function() {
        let layers = [];
        this.options.data.forEach(entry => {
            let url = 'https://www.vreme.si' + entry.path;
            
            let dt = new Date(entry.valid);
            let timestamp = dt.getDate() +'.'+ (dt.getMonth() + 1) + '.' + dt.getFullYear() +
            ' ' + entry.hhmm.slice(0,2) + ':' + entry.hhmm.slice(2,4) + ' UTC';

            let bbox = entry.bbox.split(',');

            bbox = bbox.map(coord => parseFloat(coord));
            
            let imageBounds = [bbox.slice(0,2), bbox.slice(2,)];

            let layer = L.imageOverlay(url, imageBounds, {
                opacity: this.options.opacity
            });
            
            layers.push({
                timestamp:timestamp,
                layer:layer
            });

        });

        // console.log(layers);

        return layers;
    }
});

L.control.rainfall = function (options) {
    return new L.Control.Rainfall(options);
};
