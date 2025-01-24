console.log('script.js is loaded');

document.addEventListener('DOMContentLoaded', function() {
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');

    btn.onclick = function () {
        sidebar.classList.toggle('active');
    };
});
    
    function initSignaturePad(canvasId, clearBtnId, hiddenInputId) {
    const canvas = document.getElementById(canvasId);
    const context = canvas.getContext('2d');
    let isDrawing = false;

    // Function to get the position relative to the canvas
    function getPosition(event) {
        if (event.touches) {
            const rect = canvas.getBoundingClientRect();
            return { x: event.touches[0].clientX - rect.left, y: event.touches[0].clientY - rect.top };
        } else {
            return { x: event.offsetX, y: event.offsetY };
        }
    }

    // Start drawing
    function startDrawing(event) {
        isDrawing = true;
        context.beginPath();
        const position = getPosition(event);
        context.moveTo(position.x, position.y);
        event.preventDefault();
    }

    // Draw
    function draw(event) {
        if (isDrawing) {
            const position = getPosition(event);
            context.lineTo(position.x, position.y);
            context.stroke();
            event.preventDefault(); // Prevent scrolling on touch devices
        }
    }

    // Stop drawing
    function stopDrawing(event) {
        isDrawing = false;
        event.preventDefault();
    }

    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseleave', stopDrawing);

    // Touch events
    canvas.addEventListener('touchstart', startDrawing);
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', stopDrawing);
    canvas.addEventListener('touchcancel', stopDrawing);

    // Clear canvas when the clear button is clicked
    document.getElementById(clearBtnId).addEventListener('click', function(event) {
        event.preventDefault();
        context.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById(hiddenInputId).value = '';
    });

    // Save the canvas content to the hidden input on form submission
    function saveCanvas() {
        document.getElementById(hiddenInputId).value = canvas.toDataURL('image/png');
    }

    canvas.addEventListener('mouseup', saveCanvas);
    canvas.addEventListener('touchend', saveCanvas);
}

// Initialize signature pads 
initSignaturePad('signC-pad', 'clear-signC', 'signC'); 
initSignaturePad('signA-pad', 'clear-signA', 'signA'); 
initSignaturePad('signI-pad', 'clear-signI', 'signI'); 
initSignaturePad('signS-pad', 'clear-signS', 'signS');

// On form submission
document.getElementById('update_form').addEventListener('submit', function(event) {
    // Convert all canvases to base64 and save in hidden inputs
    document.getElementById('signC').value = document.getElementById('signC-pad').toDataURL('image/png');
    document.getElementById('signA').value = document.getElementById('signA-pad').toDataURL('image/png');
    document.getElementById('signI').value = document.getElementById('signI-pad').toDataURL('image/png');
    document.getElementById('signS').value = document.getElementById('signS-pad').toDataURL('image/png');

    console.log('All signatures saved!');
    // The form will now submit with the hidden inputs
});
    