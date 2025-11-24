// Array with Romanian month names
const lunile = [
    "Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie",
    "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"
];

// Get DOM elements
const btnDetalii = document.getElementById('btnDetalii');
const divDetalii = document.getElementById('detalii');
const spanData = document.getElementById('dataProdus');

// On page load
document.addEventListener('DOMContentLoaded', function() {
    // Add the 'ascuns' class to hide details initially
    divDetalii.classList.add('ascuns');
    
    // Get current date
    const astazi = new Date();
    const zi = astazi.getDate();
    const luna = lunile[astazi.getMonth()];
    const an = astazi.getFullYear();
    
    // Format: "16 Noiembrie 2025"
    const dataFormatata = `${zi} ${luna} ${an}`;
    
    // Inject the date into the span
    spanData.textContent = dataFormatata;
});

// Button click event
btnDetalii.addEventListener('click', function() {
    // Toggle the 'ascuns' class
    divDetalii.classList.toggle('ascuns');
    
    // Change button text based on visibility
    if (divDetalii.classList.contains('ascuns')) {
        btnDetalii.textContent = 'Afiseaza detalii';
    } else {
        btnDetalii.textContent = 'Ascunde detalii';
    }
});
