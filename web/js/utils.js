function showNotification(msg, isError = false) {
  let msgToast = "";

  if (isError) {
    let errorStyle = {
      background: "linear-gradient(to right, #fb6a4a, #cb181d)",
    };
    msgToast = Toastify({
      text: msg,
      duration: 5000,
      style: errorStyle,
    });
  } else {
    msgToast = Toastify({
      text: msg,
      duration: 3000,
    });
  }

  msgToast.showToast();
}

function setSessionVariable(varName, value, onSuccess = null) {
  getParams = new URLSearchParams({
    [varName]: value,
  });

  fetch(`update_session.php?${getParams}`, { method: "GET" })
    // .then((response) => response.json())
    // success
    .then((response) => {
      // console.log(response);
      if (onSuccess) onSuccess();
    })
    // error
    .catch((error) => console.error("error:", error));
}

function refreshPage() {
  location.reload();
}

function isInputValid(el) {
  // currently handles text inputs / number inputs / checkbox inputs

  if (el.target.type === "checkbox") {
    // checkbox - no check needed
    // const isChecked = el.target.checked;
    return true;
  } else {
    // type: 'number' or 'text'
    // parse number - needed because values ALWAYS are strings
    const inputValue = parseInt(el.target.value);
    if (isNaN(inputValue)) {
      // string - no check needed
      return true;
    } else if (typeof inputValue === 'number') {
      if (
        inputValue < parseInt(el.target.min) ||
        inputValue > parseInt(el.target.max)
      ) {
        return false;
      }
    }
  }
  return true;
}
