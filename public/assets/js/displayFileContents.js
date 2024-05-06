const files = document.querySelectorAll('.file');

files.forEach(file => {
    file.addEventListener('click', () => {
        const fileName = file.querySelector('.file-name').textContent.trim();
        
        fetch(`../config/get_file.php?filename=${encodeURIComponent(fileName)}`)
            .then(response => response.ok ? response.text() : Promise.reject('Failed to fetch file'))
            .then(data => {
                const fileTitle = document.querySelector('#selected-file h3');
                const scanForm = document.querySelector('#selected-file form');
                fileTitle.style.display = 'block';
                fileTitle.innerHTML = 'Preview of <span style="color: var(--highlight-light);">' + fileName + '</span>';
                scanForm.style.display = 'block';

                const scanButton = scanForm.querySelector('#scan-button');
                scanButton.setAttribute('value', fileName);
                
                const codeElement = document.createElement('pre');
                const codeBlock = document.createElement('code');
                codeBlock.textContent = data;

                codeElement.appendChild(codeBlock);
                const textContentBox = document.getElementById('file-content');
                textContentBox.textContent = '';
                textContentBox.appendChild(codeElement);

                hljs.highlightElement(codeBlock);

                const targetSection = document.getElementById('selected-file');
                fadeIn(targetSection);

                setTimeout(() => {
                    window.scrollTo({
                        top: targetSection.offsetTop,
                        behavior: 'smooth'
                    });
                }, 300);
            });
    });
});

function fadeIn(object) {
    object.animate([
        { opacity: '0' },
        { opacity: '1' }
    ], {
        duration: 500,
        fill: 'forwards',
        easing: 'ease'
    });
}