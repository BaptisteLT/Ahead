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

        document.addEventListener('DOMContentLoaded', () => {
            this.setFiltersListeners();
            this.setDatePickers();
        });
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
        document.querySelector('#filters_dateFrom').addEventListener('change', (e) => {
            this.dateFrom = e.target.value;
            console.log(this.dateFrom);
            console.log(this.dateTo);
        });
        document.querySelector('#filters_dateTo').addEventListener('change', (e) => {
            this.dateTo = e.target.value;
            console.log(this.dateFrom);
            console.log(this.dateTo);
        });
    }

    removeSymptom(id){
        const index = this.symptoms.indexOf(id);  // Find the index of the value
        if (index !== -1) {
            this.symptoms.splice(index, 1);  // Remove the value at the found index
        }
    }
}
