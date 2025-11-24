// Array with Romanian month names
const lunile = [
    "Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie",
    "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"
];

// Get DOM elements
const inputActivitate = document.getElementById('inputActivitate');
const btnAdauga = document.getElementById('btnAdauga');
const listaActivitati = document.getElementById('listaActivitati');

// Add event listener to button
btnAdauga.addEventListener('click', adaugaActivitate);

// Allow adding activity by pressing Enter
inputActivitate.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        adaugaActivitate();
    }
});

function adaugaActivitate() {
    // Get the text from input field
    const textActivitate = inputActivitate.value.trim();
    
    // Check if the input is not empty
    if (textActivitate === '') {
        alert('Va rugam introduceti o activitate!');
        return;
    }
    
    // Get current date
    const astazi = new Date();
    const zi = astazi.getDate();
    const luna = lunile[astazi.getMonth()];
    const an = astazi.getFullYear();
    
    // Format: "Activitate - adaugata la: 16 Noiembrie 2025"
    const textComplet = `${textActivitate} - adaugata la: ${zi} ${luna} ${an}`;
    
    // Create new list item
    const liElement = document.createElement('li');
    liElement.textContent = textComplet;
    
    // Add the item to the list
    listaActivitati.appendChild(liElement);
    
    // Clear the input field
    inputActivitate.value = '';
    
    // Focus back to input field for better user experience
    inputActivitate.focus();
}
