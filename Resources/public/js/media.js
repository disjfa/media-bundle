import axios from 'axios';

document.addEventListener("DOMContentLoaded", initFileElements);

function initFileElements() {
  for (let input of document.querySelectorAll('.js-upload-form input[type=file]')) {
    input.addEventListener('change', handleFiles, false);
  }
}

function handleFiles(e) {
  const { files } = e.target;
  const csrfToken = e.target.dataset.csrfToken || e.target.form.dataset.csrfToken;

  if (!csrfToken) {
    console.warn('No csrf-token data tag on form');
    return;
  }

  for (let file of files) {
    uploadFile(file, csrfToken, e.target);
  }
}

function uploadFile(file, csrfToken, input) {
  const uploadForm = new FormData();
  uploadForm.append('upload[_token]', csrfToken);
  uploadForm.append('upload[upload]', file);
  axios
    .post('/api/upload/', uploadForm)
    .then(result => {
      const { name, url } = result.data;

      if (input.dataset.target) {
        const element = document.querySelector(input.dataset.target);
        if (element.tagName.toLowerCase() === 'input') {
          element.value = url;
        }
      }

      const div = document.createElement('div');
      div.classList.add('alert', 'alert-success');
      div.innerHTML = `Uploaded: ${name}`;

      const img = document.createElement('img');
      img.classList.add('img-fluid');
      img.src = url;

      div.appendChild(img);

      input.parentNode.append(div);
    })
    .catch(error => {
      console.log(error.response);
    });
}
