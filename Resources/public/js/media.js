import axios from 'axios';

document.addEventListener("DOMContentLoaded", initFileElements);

function initFileElements() {
  for (let input of document.querySelectorAll('.js-media-form input[type=file]')) {
    input.addEventListener('change', handleFiles, false);
  }
}

function handleFiles(e) {
  const { files } = e.target;
  const { csrfToken } = e.target.form.dataset;

  if (!csrfToken) {
    console.warn('No csrf-token data tag on form');
    return;
  }

  for (let file of files) {
    uploadFile(file, csrfToken, e.target.form);
  }
}

function uploadFile(file, csrfToken, form) {
  const uploadForm = new FormData();
  uploadForm.append('media[_token]', csrfToken);
  uploadForm.append('media[upload]', file);
  axios
    .post('/api/upload/', uploadForm)
    .then(result => {
      const { name, url } = result.data;

      const div = document.createElement('div');
      div.classList.add('alert', 'alert-success');
      div.innerHTML = `Uploaded: ${name}`;

      const img = document.createElement('img');
      img.classList.add('img-fluid');
      img.src = url;

      div.appendChild(img);

      form.append(div);
    })
    .catch(error => {
      console.log(error.response);
    });
}
