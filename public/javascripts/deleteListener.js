document.querySelectorAll(".deleteButton").forEach(clickListener);

function clickListener(elem) {
  elem.addEventListener("click", e => {
    let id = elem.id;
    let password = prompt("Enter the delete password please.");
    console.log(`Deleting row for ID ${id}`);
    sendXHR(id, password);
  })
}

function sendXHR(id, password) {
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = e => {
    if (this.readyState == 4 && this.status == 200) {
      console.log("Deleted !")
    }
  };
  xhr.open("DELETE", `/list/delete?id=${id}&password=${password}`, true);
  xhr.send();
}