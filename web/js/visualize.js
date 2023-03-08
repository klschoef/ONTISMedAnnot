// pagination
let pageInput = null;
let pageSubmitForm = null;

// annotations
let deleteEntryButtons = null;

document.addEventListener("DOMContentLoaded", (event) => {
    // pagination
    pageInput = document.querySelector("#pageChangeInput");
    pageSubmitForm = document.querySelector("#pageChangeForm");
    pageInput.addEventListener("change", updatePageValue);

    // options variables
    const optionsElements = ["image_width", "num_items", "review_mode"];

    for (const o of optionsElements) {
        let inputEl = document.querySelector(`#${o}-input`);
        inputEl.addEventListener("change", (e) => {
            let inputValue = e.target.value;
            if (e.target.type === "checkbox") {
                inputValue = e.target.checked;
            }
            if (!isInputValid(e)) {
                showNotification(
                    `Value must be between ${e.target.min} and ${e.target.max}`,
                    true
                );
                return;
            }
            setSessionVariable(o, inputValue, () => {
                refreshPage();
            });
        });
    }

    // annotations
    deleteEntryButtons = document.querySelectorAll(".deleteEntryButton");
    for (let b of deleteEntryButtons) {
        b.addEventListener("click", confirmDeleteEntry);
    }

    // lightbox
    let gallery = new SimpleLightbox(".image-wrapper a", {
        /* options */
    });
    gallery.on("show.simplelightbox", function () {
        // Do something…
        // console.log("SHOW");
    });
    gallery.on("error.simplelightbox", function (e) {
        console.log(e); // Some useful information
    });

    updateClipboardDisplay();

    // shows userinfo posted to #userInfo div via php
    showUserInfo();
});

function showUserInfo() {
    let userInfoEl = document.querySelector("#userInfo");
    let userInfoVal = userInfoEl.innerHTML;
    if (userInfoVal !== "") {
        let isError = userInfoVal.toLowerCase().includes("error")
            ? true
            : false;
        showNotification(userInfoVal, isError);
    }
    userInfoVal.value = "";
}

function updateClipboardDisplay() {
    // fill clipboard display
    pasteFromClipboard("clipboard-contents", true);
}

function clearClipboardDisplay() {
    copyToClipboard(-1, true);
}

function replaceContent(id) {
    let toReplaceEl = document.querySelector(`#input_${id}`);
    let updateClipboardDisplayEl = document.querySelector(
        "#input_clipboard-contents"
    );
    let toReplaceContent = toReplaceEl.value;
    pasteFromClipboard(id, false, () => {
        // copy replaced text to clipboard
        updateClipboardDisplayEl.value = toReplaceContent;
        copyToClipboard("clipboard-contents");
    });
}

function updatePageValue(e) {
    const inputValue = e.target.value;
    if (!isInputValid(e)) {
        return;
    }
    pageSubmitForm.submit();
}

function confirmDeleteEntry(e) {
    // returns boolean (OK = true, Cancel = false)
    let doDelete = confirm("Are you sure to delete this annotation?");
    if (!doDelete) {
        e.preventDefault();
        // window.history.back();
        return;
    }
}

function saveAll(onSuccess) {
    let doSave = confirm("Are you sure to save all annotations?");

    if (doSave) {
        let allInputs = document.querySelectorAll("[id^='input_']");
        let allIds = [];
        for (const i of allInputs) {
            let idStr = i.id.split("_")[1];
            let id = parseInt(idStr) || null;
            if (id !== null) {
                checkElValue = document.querySelector(`#check-${id}`).checked
                    ? 1
                    : 0;
                allIds.push({
                    id: id,
                    caption: i.value,
                    reviewed: checkElValue,
                });
            }
        }

        let data = { save_ids: allIds };
        // console.log(data);

        fetch(`save_all.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                // 'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            // success
            .then((response) => {
                // console.log(response);
                if ("msg" in response) {
                    if (response["msg"].toLowerCase().includes("ok")) {
                        setSessionVariable(
                            "user_info",
                            "Saved all annotations!"
                        );
                        refreshPage();
                    } else {
                        showNotification(response["msg"], true);
                    }
                }
                if (onSuccess) onSuccess();
            })
            // error
            .catch((error) => console.error("error:", error));
    }
}

function copyToClipboard(id, silent = false) {
    // -1 clears clipboard
    let textToCopy = "";
    if (id != -1) {
        let inputEl = document.getElementById(`input_${id}`);
        textToCopy = inputEl.value;
    }
    // console.log(textToCopy);

    // navigator clipboard api needs a secure context (https)
    if (navigator.clipboard && window.isSecureContext) {
        if (!silent) {
            showNotification(`Copied to clipboard: '${textToCopy}'`);
        }
        // navigator clipboard api method'
        res = navigator.clipboard.writeText(textToCopy);
        updateClipboardDisplay();
        return res;
    }

    // connection is not secure - try workaround

    try {
        // text area method
        const textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        // make the textarea out of viewport
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((res, rej) => {
            // here the magic happens
            document.execCommand("copy") ? res() : rej();
            textArea.remove();

            if (!silent) {
                showNotification(`Copied to clipboard: '${textToCopy}'`);
            }
            updateClipboardDisplay();
        });
    } catch (error) {
        console.error(error);
        // expected output: ReferenceError: nonExistentFunction is not defined
        // Note - error messages will vary depending on browser
        // failed
        showNotification(`Failed to copy to clipboard: '${textToCopy}'`, true);

        return Promise.reject("The Clipboard API is not available.");
    }
}

function pasteFromClipboard(id, silent = false, cb = null) {
    let targetEl = document.getElementById(`input_${id}`);
    navigator.clipboard
        .readText()
        .then((text) => {
            targetEl.value = text;
            if (!silent) {
                showNotification(`Pasted content: ${text}`);
            }
            if (cb) {
                cb();
            }
        })
        .catch((err) => {
            showNotification(`Failed to read clipboard contents: ${err}`, true);
            if (cb) {
                cb();
            }
        });
}

function exportCSV() {
    let includeUnchecked = confirm(
        "Include not reviewed (unchecked) annotations?"
    );

    let data = { include_unchecked: includeUnchecked ? 1 : 0 };
    // console.log(data);

    fetch(`export.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        // success
        .then((response) => {
            // console.log(response);
            if ("msg" in response) {
                if (response["msg"].toLowerCase().includes("error")) {
                    showNotification(response["msg"], true);
                } else {
                    let fileUrl = response["msg"];
                    let fileName = fileUrl.replace("tmp/", "");
                    console.log(`Created ${fileUrl}`);
                    downloadURI(fileUrl, fileName);
                }
            }
        })
        // error
        .catch((error) => console.error("error:", error));
}

function downloadURI(uri, name) {
    let link = document.createElement("a");
    link.download = name;
    link.href = uri;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    delete link;
}
