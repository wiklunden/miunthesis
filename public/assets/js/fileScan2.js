document.getElementById('scan-button').addEventListener('click', function() {
    const fileName = this.value;
    
    fetch(`../config/scan_file.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `scan-file=${encodeURIComponent(fileName)}`
    })
    .then(response => response.json())
    .then(data => {
        const insertsMissingExecute = data.insertsMissingExecute;

        const codeBlock = document.getElementById('file-content');

        insertsMissingExecute.forEach(pos => {
            highlightTextAtPosition(codeBlock, pos, 'insert');
        });

        const textContentBox = document.getElementById('file-content');
        textContentBox.innerHTML = ''; // Resets content
        textContentBox.appendChild(codeElement);

        hljs.highlightElement(codeBlock);
    });
});

function highlightTextAtPosition(element, position, word) {
    let text = element.textContent;
    let before = text.substring(0, position);
    let after = text.substring(position + word.length);
    let highlighted = `<span style="background-color:yellow;">${text.substring(position, position + word.length)}</span>`;

    element.innerHTML = before + highlighted + after;
}