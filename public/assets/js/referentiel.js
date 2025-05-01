// function previewImage(input) {
//     const preview = document.getElementById('imagePreview');
//     const file = input.files[0];
//     const errorMessage = input.parentElement.querySelector('.error-message');

//     // Validation du format et de la taille
//     const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
//     const maxSize = 2 * 1024 * 1024; // 2MB

//     if (!validTypes.includes(file.type)) {
//         errorMessage.textContent = 'Format invalide. Utilisez JPG ou PNG.';
//         input.value = '';
//         return;
//     }

//     if (file.size > maxSize) {
//         errorMessage.textContent = 'L\'image ne doit pas dépasser 2MB.';
//         input.value = '';
//         return;
//     }

//     errorMessage.textContent = '';
//     const reader = new FileReader();
//     reader.onload = function(e) {
//         preview.src = e.target.result;
//     }
//     reader.readAsDataURL(file);
// }

// function closeModal() {
//     document.getElementById('createReferentielModal').style.display = 'none';
// }

// document.addEventListener('DOMContentLoaded', function() {
//     const form = document.getElementById('referentielForm');
    
//     form.addEventListener('submit', async function(e) {
//         e.preventDefault();
        
//         const formData = new FormData(this);
        
//         try {
//             const response = await fetch('?page=add-referentiel-process', {
//                 method: 'POST',
//                 body: formData
//             });
            
//             const data = await response.json();
            
//             if (data.success) {
//                 closeModal();
//                 window.location.reload();
//             } else {
//                 alert(data.message);
//             }
//         } catch (error) {
//             console.error('Erreur:', error);
//         }
//     });
// });