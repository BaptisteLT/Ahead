import { Controller } from '@hotwired/stimulus';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css"; // Import the CSS for styling

export default class extends Controller {

    dateFrom = null;
    dateTo = null;
    diseaseId = null;
    symptoms = [];

    initialize() {
        this._onPreConnect = this._onPreConnect.bind(this);
        this._onConnect = this._onConnect.bind(this);
    }

    connect() {
        this.element.addEventListener('autocomplete:pre-connect', this._onPreConnect);
        this.element.addEventListener('autocomplete:connect', this._onConnect);

        this.setFiltersListeners();
        this.setDatePickers();
        this.doSearch();
    }

    disconnect() {
        this.element.removeEventListener('autocomplete:connect', this._onConnect);
        this.element.removeEventListener('autocomplete:pre-connect', this._onPreConnect);
    }

    _onPreConnect(event) {
        // Modify Tom Select options before initialization
        event.detail.options.onChange = (value) => {
            // Only for the symptoms select
            if(event.target.id === 'filters_symptoms')
            {
                const selectedElement = this.element.querySelector(`.ts-dropdown-content [data-value="${value}"]`);
                if (selectedElement) {
                    // If the element has been added before, we remove it
                    if(this.symptoms.includes(value)){
                        this.removeSymptom(value);  // Use the new function to remove symptom

                        const element = document.querySelector('.symptom[data-id="'+value+'"]');
                        if (element) {
                            element.remove();  // Remove the element from DOM
                            console.log('Element removed');
                        } else {
                            console.log('Element not found');
                        }
                        this.doSearch();
                    }
                    // Else we create a new element
                    else{
                        const selectedText = selectedElement.innerText;
                        let element = this.createSymptomListElement(value, selectedText);
                        document.querySelector('#symptoms_list').append(element);
                    }
                } else {
                    console.error('Element not found for value:', value);
                }
            }
            else if(event.target.id === 'filters_diseases'){
                this.diseaseId = value;
                console.log(this.diseaseId);
                this.doSearch();


            }
        };
    }

    _onConnect(event) {
        // TomSelect has been initialized
        console.log('TomSelect instance:', event.detail.tomSelect);
        console.log('Options used to initialize TomSelect:', event.detail.options);

        const tomSelect = event.detail.tomSelect; // Get the Tom Select instance
        if(event.target.id === 'filters_symptoms'){
            tomSelect.on('change', () => {
                tomSelect.setValue(''); // Clear the input
            });
        }
    }

    setDatePickers(){
        const dateFrom = document.querySelector('#filters_dateFrom');
        const dateTo = document.querySelector('#filters_dateTo');

        flatpickr(dateFrom, {
            dateFormat: "d/m/Y", // Set your desired date format
            allowInput: true,    // Allows typing in the input
        });
        flatpickr(dateTo, {
            dateFormat: "d/m/Y", // Set your desired date format
            allowInput: true,    // Allows typing in the input
        });
    }

    createSymptomListElement(id, text){
        // Add the symptom to the list
        if (!this.symptoms.includes(id)) {
            this.symptoms.push(id);
        }
        console.log(this.symptoms);

        const symptomDiv = document.createElement('div');
        symptomDiv.classList.add('symptom');
        symptomDiv.setAttribute('data-id', id);

        const symptomNameSpan = document.createElement('span');
        symptomNameSpan.classList.add('symptom-name');
        symptomNameSpan.textContent = text;

        const button = document.createElement('button');
        button.setAttribute('type', 'button');
        button.textContent = 'Supprimer';
        button.addEventListener('click', () => {
            symptomDiv.remove();
            this.removeSymptom(id);  // Use the function to remove symptom
            console.log(this.symptoms);
        });

        symptomDiv.appendChild(symptomNameSpan);
        symptomDiv.appendChild(button);

        return symptomDiv;
    }

    setFiltersListeners(){
        let dateTo = document.querySelector('#filters_dateTo');
        let dateFrom = document.querySelector('#filters_dateFrom');
        this.dateFrom = dateFrom.value;
        this.dateTo = dateTo.value;
        dateFrom.addEventListener('change', (e) => {
            this.dateFrom = e.target.value;
            console.log(this.dateFrom);
            console.log(this.dateTo);
            this.doSearch();
        });
        dateTo.addEventListener('change', (e) => {
            this.dateTo = e.target.value;
            console.log(this.dateFrom);
            console.log(this.dateTo);
            this.doSearch();
        });
    }

    removeSymptom(id){
        const index = this.symptoms.indexOf(id);  // Find the index of the value
        if (index !== -1) {
            this.symptoms.splice(index, 1);  // Remove the value at the found index
        }
    }

    /**
     * Lance la recherche avec les filtres utilisés
     */
    doSearch(){
        const form = new FormData(); // Récupère les données du formulaire
        form.append('dateFrom', this.dateFrom);
        form.append('dateTo', this.dateTo);
        form.append('diseaseId', this.diseaseId);
        form.append('symtoms', JSON.stringify(this.symptoms));
    
        // Utilise fetch pour envoyer une requête GET
        fetch('/api/filters', {
            method: 'POST',
            body: form
        })
        .then(response => response.json()) // Gère la réponse en JSON
        .then(data => {
            console.log(data); // Traite les données de la réponse
            this.addMarkersToMap(data);
        })
        .catch(error => {
            console.error('Erreur:', error); // Gère les erreurs
        });
      
    }
    addMarkersToMap(data) {
        this.removeCircles();
    
        let map = document.querySelector('#features');
    
        // Function to set up the modal for the circles
        const setupCircleEvent = (circle) => {
            circle.addEventListener('mouseenter', (event) => {
                event.stopPropagation(); // Prevent event bubbling
                showModal(circle); // Show modal for the hovered circle
            });
    
            circle.addEventListener('mouseleave', () => {
                hideModal(); // Hide modal when mouse leaves the circle
            });
        };
    
        // Function to position and display the modal
        const showModal = (circle) => {
            const modal = document.getElementById('map-modal');
            const circleRect = circle.getBoundingClientRect();
            const size = parseInt(circle.getAttribute('r')); // Get radius
            const svg = document.querySelector('svg');
            const svgRect = svg.getBoundingClientRect();
    
            modal.innerHTML = '<div>Région: ' + circle.dataset.region + '</div><br/><div>Nombre de cas: ' + circle.dataset.countreports + '</div>';


            // Calculate the modal position based on the circle's position within the SVG
            const modalX = circleRect.left - svgRect.left + circleRect.width / 2; // Center the modal
            const modalY = circleRect.top - svgRect.top + circleRect.height; // Position below the circle
    
            console.log('Circle size: ' + size);
    
            // Position the modal
            modal.style.left = `${modalX + size}px`; // Adjust position
            modal.style.top = `${modalY}px`;
            modal.style.display = 'block'; // Show modal
        };
    
        // Function to hide the modal
        const hideModal = () => {
            const modal = document.getElementById('map-modal');
            modal.style.display = 'none'; // Hide modal
        };
    
        data.forEach(item => {
            const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    
            console.log(item)
            // Set attributes
            circle.setAttribute('cx', item.x);
            circle.setAttribute('cy', item.y);
            circle.setAttribute('r', item.pixelsSize);
            circle.setAttribute('data-countreports', item.countReports)
            circle.setAttribute('data-region', item.name)
            circle.classList.add('map-circle');
    
            // Set color based on size
            let color = '';
            if(item.pixelsSize <= 20) {
                // Green
                color = '#28a745CC';
            } else if(item.pixelsSize <= 40) {
                // Orange
                color = '#ff7e1bCC';
            } else {
                // Red
                color = '#ff2c2cCC';
            }
            
            console.log(item.pixelsSize);
            circle.setAttribute('fill', color);
    
            // Append the circle to the SVG map
            map.append(circle);
            console.log('Region:', item.name); // Assuming each item has a 'name' property
            console.log('Report Count:', item.report_count); // Assuming each item has a 'report_count' property
    
            // Call the function to set up the hover event for this circle
            setupCircleEvent(circle);
        });
    }

    removeCircles(){
        // Select all elements with the class 'map-circle'
        const circles = document.querySelectorAll('.map-circle');

        // Loop through the NodeList and remove each element
        circles.forEach(circle => {
            circle.remove(); // Remove the element from the DOM
        });
    }
}
