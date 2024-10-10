import { Controller } from '@hotwired/stimulus';
import Swal from 'sweetalert2';

export default class extends Controller {

    step = 1;
    symptoms = [];

    connect() {
        this.element.addEventListener('autocomplete:pre-connect', this._onPreConnect);
        this.element.addEventListener('autocomplete:connect', this._onConnect);

        this.fetchForm();
    }

    initialize() {
        this._onPreConnect = this._onPreConnect.bind(this);
        this._onConnect = this._onConnect.bind(this);
    }

    disconnect() {
        this.element.removeEventListener('autocomplete:connect', this._onConnect);
        this.element.removeEventListener('autocomplete:pre-connect', this._onPreConnect);
    }

    _onPreConnect(event) {
        // Modify Tom Select options before initialization
        event.detail.options.onChange = (value) => {
            // Only for the symptoms select
            if(event.target.id === 'search_symptoms_symptoms')
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
        };
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

    _onConnect(event) {
        // TomSelect has been initialized
        console.log('TomSelect instance:', event.detail.tomSelect);
        console.log('Options used to initialize TomSelect:', event.detail.options);
        
        const tomSelect = event.detail.tomSelect; // Get the Tom Select instance
        if(event.target.id === 'search_symptoms_symptoms'){
            tomSelect.on('change', () => {
                tomSelect.setValue(''); // Clear the input
            });
        }
    }

    removeSymptom(id){
        const index = this.symptoms.indexOf(id);  // Find the index of the value
        if (index !== -1) {
            this.symptoms.splice(index, 1);  // Remove the value at the found index
        }
    }

    /**
     * Fetch the form from the backend
     */
    fetchForm() {
        fetch('api/load-form/'+this.step)
            /*.then(response => {
                if (!response.ok) {
                    throw new Error('Impossible de récupérer l\'étape suivante du formulaire');
                }
                return response.text();
            })*/
            .then(response => {
                if (!response.ok) {
                    this.displayErrorModal('Une erreur est survenue');
                }
        
                const contentType = response.headers.get('content-type');   
                console.log(contentType);
                
                if (contentType && contentType.includes('application/json')) {
                    return response.json(); // If the content is JSON
                } else {
                    return response.text(); // If the content is plain text
                }
            })
            .then(data => {
                // Last step, we redirect to an URL
                if (typeof data === 'object') {
                    //Afficher un message et attendre 5 secondes avant de rediriger l'utilisateur
                    document.querySelector('#form-container').innerHTML = '<h1 class="text-center mb-5">Merci pour votre contribution!</h1>';
                    setTimeout(() => {
                        window.location.href = data.redirectUrl;
                    }, 3000)
                    
                // Add the HTML Form
                } else {
                    document.querySelector('#form-container').innerHTML = data;
                    const form = document.querySelector('#form-container form');
                    const submitBtn = document.querySelector('#submit-step');
                    //We create an event listener on the "Continuer" button
                    submitBtn.addEventListener('click', (event) => {
                        event.preventDefault(); // Prevent default form submission
                        this.submitForm(form);   // Call the function to submit the form
                    });
                }
               
            })
            .catch(error => {
                this.displayErrorModal(error);
                //alert('There has been a problem with your fetch operation: '+error);
            });
    }

    /**
     * Submit the form to the controller
     * @param {*} form 
     */
    submitForm(form){
        let formData;
        
        if(form.getAttribute('name') === 'search_symptoms'){
            formData = new FormData(); // Create FormData from the form
            formData.append('symptoms', JSON.stringify(this.symptoms));
        }
        else{
            formData = new FormData(form); // Create FormData from the form
        }

        fetch('api/submit-form/'+this.step, {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error); // Get error message from JSON
                });
            }
            return response.json(); // Parse the JSON response (assuming the server returns JSON)
        })
        .then(data => {
            console.log('Form submitted successfully:', data);
            // Handle success (e.g., show a success message or load the next step)
            this.step = this.step + 1;
            this.fetchForm();
        })
        .catch(error => {
            //TODO modal error
            this.displayErrorModal(error.message);
            //alert('There has been a problem with your form submission: ' + error.message);
        });
    }
    displayErrorModal(error){
        Swal.fire({
            title: 'Erreur',
            text: error,
            icon: 'error',
            confirmButtonText: 'Ok'
          })
    }
}
