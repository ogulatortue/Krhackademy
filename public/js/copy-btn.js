document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.getElementById('copy-button');
    const textToCopy = document.getElementById('text-to-copy').textContent.trim();

    copyButton.addEventListener('click', () => {
        if (!navigator.clipboard) {
            fallbackCopyTextToClipboard(textToCopy);
            return;
        }

        navigator.clipboard.writeText(textToCopy).then(() => {
            showSuccess();
        }).catch(err => {
            console.error('Erreur (API moderne) : Impossible de copier le texte.', err);
            showError();
        });
    });

    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showSuccess();
            } else {
                showError();
            }
        } catch (err) {
            console.error('Erreur (Ancienne méthode) : Impossible de copier le texte.', err);
            showError();
        }

        document.body.removeChild(textArea);
    }
    
    const originalContent = copyButton.innerHTML;

    function showSuccess() {
        copyButton.innerHTML = '<i class="fas fa-check"></i> Copié';
        copyButton.disabled = true;
        setTimeout(() => {
            copyButton.innerHTML = originalContent;
            copyButton.disabled = false;
        }, 2000);
    }

    function showError() {
        copyButton.innerHTML = 'Erreur';
    }
});