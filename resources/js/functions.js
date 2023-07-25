'use strict';

// Cookie law banner
document.querySelector('#cookie-banner-dismiss') && document.querySelector('#cookie-banner-dismiss').addEventListener('click', function () {
    setCookie('cookie_law', 1, new Date().getTime() + (10 * 365 * 24 * 60 * 60 * 1000), '/');
    document.querySelector('#cookie-banner').classList.add('d-none');
});

// Dark mode
document.querySelector('#dark-mode') && document.querySelector('#dark-mode').addEventListener('click', function (e) {
    e.preventDefault();

    // Update the sources
    document.querySelectorAll('[data-theme-target]').forEach(function (element) {
        element.setAttribute(element.dataset.themeTarget, document.querySelector('html').classList.contains('dark') ? element.dataset.themeLight : element.dataset.themeDark);
    });

    // Update the text
    this.querySelector('span').textContent = document.querySelector('html').classList.contains('dark') ? this.querySelector('span').dataset.textLight : this.querySelector('span').dataset.textDark;

    // Update the dark mode cookie
    setCookie('dark_mode', (document.querySelector('html').classList.contains('dark') ? 0 : 1), new Date().getTime() + (10 * 365 * 24 * 60 * 60 * 1000), '/');

    // Update the CSS class
    if (document.querySelector('html').classList.contains('dark')) {
        document.querySelector('html').classList.remove('dark');
    } else {
        document.querySelector('html').classList.add('dark');
    }
});

// Pricing plans
document.querySelector('#plan-month') && document.querySelector('#plan-month').addEventListener("click", function () {
    document.querySelectorAll('.plan-month').forEach(element => element.classList.add('d-block'));
    document.querySelectorAll('.plan-year').forEach(element => element.classList.remove('d-block'));
});

document.querySelector('#plan-year') && document.querySelector('#plan-year').addEventListener("click", function () {
    document.querySelectorAll('.plan-year').forEach(element => element.classList.add('d-block'));
    document.querySelectorAll('.plan-month').forEach(element => element.classList.remove('d-block', 'plan-preload'));
});

let updateSummary = (type) => {
    if (type == 'month') {
        document.querySelectorAll('.checkout-month').forEach(function (element) {
            element.classList.add('d-inline-block');
        });

        document.querySelectorAll('.checkout-year').forEach(function (element) {
            element.classList.remove('d-inline-block');
        });
    } else {
        document.querySelectorAll('.checkout-month').forEach(function (element) {
            element.classList.remove('d-inline-block');
        });

        document.querySelectorAll('.checkout-year').forEach(function (element) {
            element.classList.add('d-inline-block');
        });
    }
};

let updateBillingType = (value) => {
    // Show the offline instructions
    if (value == 'bank') {
        document.querySelector('#bank-instructions').classList.remove('d-none');
        document.querySelector('#bank-instructions').classList.add('d-block');
    }
    // Hide the offline instructions
    else {
        if (document.querySelector('#bank-instructions')) {
            document.querySelector('#bank-instructions').classList.add('d-none');
            document.querySelector('#bank-instructions').classList.remove('d-block');
        }
    }

    if (value == 'cryptocom' || value == 'coinbase' || value == 'bank') {
        document.querySelectorAll('.checkout-subscription').forEach(function (element) {
            element.classList.remove('d-block');
        });

        document.querySelectorAll('.checkout-subscription').forEach(function (element) {
            element.classList.add('d-none');
        });

        document.querySelectorAll('.checkout-one-time').forEach(function (element) {
            element.classList.add('d-block');
        });

        document.querySelectorAll('.checkout-one-time').forEach(function (element) {
            element.classList.remove('d-none');
        });
    } else {
        document.querySelectorAll('.checkout-subscription').forEach(function (element) {
            element.classList.remove('d-none');
        });

        document.querySelectorAll('.checkout-subscription').forEach(function (element) {
            element.classList.add('d-block');
        });

        document.querySelectorAll('.checkout-one-time').forEach(function (element) {
            element.classList.add('d-none');
        });

        document.querySelectorAll('.checkout-one-time').forEach(function (element) {
            element.classList.remove('d-block');
        });
    }
}

// Payment form
if (document.querySelector('#form-payment')) {
    let url = new URL(window.location.href);

    document.querySelectorAll('[name="interval"]').forEach(function (element) {
        if (element.checked) {
            updateSummary(element.value);
        }

        // Listen to interval changes
        element.addEventListener('change', function () {
            // Update the URL address
            url.searchParams.set('interval', element.value);

            history.pushState(null, null, url.href);

            updateSummary(element.value);
        });
    });

    document.querySelectorAll('[name="payment_processor"]').forEach(function (element) {
        if (element.checked) {
            updateBillingType(element.value);
        }

        // Listen to payment processor changes
        element.addEventListener('change', function () {
            // Update the URL address
            url.searchParams.set('payment', element.value);

            history.pushState(null, null, url.href);

            updateBillingType(element.value);
        });
    });

    // If the Add a coupon button is clicked
    document.querySelector('#coupon') && document.querySelector('#coupon').addEventListener('click', function (e) {
        e.preventDefault();

        // Hide the link
        this.classList.add('d-none');

        // Show the coupon input
        document.querySelector('#coupon-input').classList.remove('d-none');

        // Enable the coupon input
        document.querySelector('input[name="coupon"]').removeAttribute('disabled');
    });

    // If the Cancel coupon button is clicked
    document.querySelector('#coupon-cancel') && document.querySelector('#coupon-cancel').addEventListener('click', function (e) {
        e.preventDefault();

        document.querySelector('#coupon').classList.remove('d-none');

        // Hide the coupon input
        document.querySelector('#coupon-input').classList.add('d-none');

        // Disable the coupon input
        document.querySelector('input[name="coupon"]').setAttribute('disabled', 'disabled');
    });

    // If the country value changes
    document.querySelector('#i-country').addEventListener('change', function () {
        // Remove the submit button
        document.querySelector('#form-payment').submit.remove();

        // Submit the form
        document.querySelector('#form-payment').submit();
    });
}

// Coupon form
if (document.querySelector('#form-coupon')) {
    document.querySelector('#i-type').addEventListener('change', function () {
        if (document.querySelector('#i-type').value == 1) {
            document.querySelector('#form-group-redeemable').classList.remove('d-none');
            document.querySelector('#form-group-discount').classList.add('d-none');
            document.querySelector('#i-percentage').setAttribute('disabled', 'disabled');
        } else {
            document.querySelector('#form-group-redeemable').classList.add('d-none');
            document.querySelector('#form-group-discount').classList.remove('d-none');
            document.querySelector('#i-percentage').removeAttribute('disabled');
        }
    });
}

// Table filters
document.querySelector('#search-filters') && document.querySelector('#search-filters').addEventListener('click', function (e) {
    e.stopPropagation();
})

// Clipboard
new ClipboardJS('[data-clipboard="true"]');

document.querySelectorAll('[data-clipboard-copy]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        e.preventDefault();

        try {
            let value = this.dataset.clipboardCopy;
            let tempInput = document.createElement('input');

            document.body.append(tempInput);

            // Set the input's value to the url to be copied
            tempInput.value = value;

            // Select the input's value to be copied
            tempInput.select();

            // Copy the url
            document.execCommand("copy");

            // Remove the temporary input
            tempInput.remove();
        } catch (e) { }
    });
});

// Tooltip
jQuery('[data-tooltip="true"]').tooltip({ animation: true, trigger: 'hover', boundary: 'window' });

// Copy tooltip
jQuery('[data-tooltip-copy="true"]').tooltip({ animation: true });

document.querySelectorAll('[data-tooltip-copy="true"]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        // Update the tooltip
        jQuery(this).tooltip('hide').attr('data-original-title', this.dataset.textCopied).tooltip('show');
    });

    element.addEventListener('mouseleave', function () {
        this.setAttribute('data-original-title', this.dataset.textCopy);
    });
});

// Slide menu
document.querySelectorAll('.slide-menu-toggle').forEach(function (element) {
    element.addEventListener('click', function () {
        document.querySelector('#slide-menu').classList.toggle('active');
    });
});

// Toggle password visibility
document.querySelectorAll('[data-password]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        let passwordInput = document.querySelector('#' + this.dataset.password);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            jQuery(this).tooltip('hide').attr('data-original-title', this.dataset.passwordHide).tooltip('show');
        } else {
            passwordInput.type = 'password';
            jQuery(this).tooltip('hide').attr('data-original-title', this.dataset.passwordShow).tooltip('show');
        }
    });
});

/**
 * Handle the confirmation modal event.
 *
 * @param element
 */
let confirmationModalEvent = (element) => {
    element.addEventListener('click', function () {
        // Unset attributes if previously set
        document.querySelector('#modal-button').removeAttribute('name');
        document.querySelector('#modal-button').removeAttribute('value');

        // Set the attributes
        if (this.dataset.buttonName) {
            document.querySelector('#modal-button').setAttribute('name', this.dataset.buttonName);
        }
        if (this.dataset.buttonValue) {
            document.querySelector('#modal-button').setAttribute('value', this.dataset.buttonValue);
        }
        document.querySelector('#modal-label').textContent = this.dataset.title
        document.querySelector('#modal-button').textContent = this.dataset.title;
        document.querySelector('#modal-button').setAttribute('class', this.dataset.button);
        document.querySelector('#modal-text').textContent = this.dataset.text;
        document.querySelector('#modal-sub-text').textContent = this.dataset.subText;
        document.querySelector('#modal form').setAttribute('action', this.dataset.action);
    });
}

document.querySelectorAll('[data-target="#modal"]').forEach(function (element) {
    confirmationModalEvent(element);
});

// Button loader
document.querySelectorAll('[data-button-loader]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        // Stop the button from being re-submitted while loading
        if (this.classList.contains('disabled')) {
            e.preventDefault();
        }
        this.classList.add('disabled');
        this.querySelector('.spinner-border').classList.remove('d-none');
        this.querySelector('.spinner-text').classList.add('invisible');
    });
});

/**
 * Restore a disabled button.
 */
let restoreDisabledButton = (element) => {
    element.classList.remove('disabled');
    element.querySelector('.spinner-border').classList.add('d-none');
    element.querySelector('.spinner-text').classList.remove('invisible');
}

// AI form
if (document.querySelector('#ai-form')) {
    // If the show AI form button is present
    document.querySelector('#ai-form-show-button') && document.querySelector('#ai-form-show-button').addEventListener('click', function () {
        // Hide the show AI form button
        document.querySelector('#ai-form-show-button').classList.add('d-none');

        // Show the AI form
        document.querySelector('#ai-form') && document.querySelector('#ai-form').classList.remove('d-none');

        /*autoResizeTextarea();*/
    });

    document.querySelectorAll('[data-button-loader]').forEach(function (element) {
        element.addEventListener('click', function () {
            // If any results are present, hide them
            document.querySelector('#ai-results') && document.querySelector('#ai-results').classList.add('d-none');

            // Show the placeholder container
            document.querySelector('#ai-placeholder-results').classList.remove('d-none');
            document.querySelector('#ai-placeholder-results').classList.add('d-flex');

            // Hide the default placeholder text
            document.querySelector('#ai-placeholder-text-start').classList.add('d-none');

            // Show the in-progress placeholder text
            document.querySelector('#ai-placeholder-text-progress').classList.remove('d-none');
            document.querySelector('#ai-placeholder-text-progress').classList.add('d-flex');
        });
    });
}

// Templates filters
if (document.querySelector('#template-filters')) {
    let filterTemplates = (search, category) => {
        let hideCategoryLabels = () => {
            document.querySelectorAll('[data-category-label]').forEach(function (element) {
                element.classList.add('d-none');
            });
        }

        let hideTemplates = () => {
            document.querySelectorAll('[data-template]').forEach(function (element) {
                element.classList.add('d-none');
            });
        }

        let showCategoryLabels = () => {
            document.querySelectorAll('[data-category-label]').forEach(function (element) {
                element.classList.remove('d-none');
            });
        }

        let showTemplates = () => {
            document.querySelectorAll('[data-template]').forEach(function (element) {
                element.classList.remove('d-none');
            });
        }

        let showCategoryLabel = (name) => {
            document.querySelector('[data-category-label="' + name + '"]').classList.remove('d-none');
        }

        let showTemplate = (name) => {
            document.querySelector('[data-template="' + name + '"]').classList.remove('d-none');
        }

        let templates = category ? document.querySelectorAll('[data-template-category="' + category + '"]') : document.querySelectorAll('[data-template]');

        if (!search && !category) {
            showCategoryLabels();
            showTemplates();
        } else {
            hideCategoryLabels();
            hideTemplates();

            templates.forEach(function (item) {
                if (search) {
                    if (item.dataset.template.toLowerCase().includes(search.toLowerCase())) {
                        showTemplate(item.dataset.template);
                        showCategoryLabel(item.dataset.templateCategory);
                    }
                } else {
                    showTemplate(item.dataset.template);
                    showCategoryLabel(item.dataset.templateCategory);
                }
            });
        }
    }

    document.querySelectorAll('[data-filter-category]').forEach(function (button) {
        // Listen for category button click
        button.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('[data-filter-category]').forEach(function (element) {
                // Remove the previous active button state
                element.classList.remove(element.dataset.textColorActive, element.dataset.textColorInactive);
                element.classList.add(element.dataset.textColorInactive);

                // Remove the previous active category
                element.removeAttribute('data-filter-category-active');
                element.setAttribute('href', '#');
            });
            // Set the active button state
            this.classList.remove(this.dataset.textColorInactive);
            this.classList.add(this.dataset.textColorActive);

            // Set the active category
            this.setAttribute('data-filter-category-active', this.dataset.filterCategory);
            this.removeAttribute('href');

            filterTemplates(document.querySelector('#i-search').value, document.querySelector('[data-filter-category-active]').dataset.filterCategoryActive);
        });
    });

    if (document.querySelector('#form-templates-search')) {
        document.querySelector('#i-search').addEventListener('keyup', function () {
            // If the search input has any value
            if (document.querySelector('#i-search').value.length > 0) {
                filterTemplates(document.querySelector('#i-search').value, document.querySelector('[data-filter-category-active]').dataset.filterCategoryActive);

                // Show the clear button
                document.querySelector('#clear-button-container').classList.remove('d-none');
                document.querySelector('#i-search').after(document.querySelector('#clear-button-container'));
            } else {
                filterTemplates('', document.querySelector('[data-filter-category-active]').dataset.filterCategoryActive);

                // Hide the clear button
                document.querySelector('#form-templates-search').append(document.querySelector('#clear-button-container'));
                document.querySelector('#clear-button-container').classList.add('d-none');
            }
        });

        document.querySelector('#b-clear').addEventListener('click', function () {
            // Empty the search input
            document.querySelector('#i-search').value = '';

            // Focus the search input
            document.querySelector('#i-search').focus();

            // Move the clear button outside the search input container
            document.querySelector('#form-templates-search').append(document.querySelector('#clear-button-container'));

            // Hide the clear button
            document.querySelector('#clear-button-container').classList.add('d-none');

            // Show the filtered items
            filterTemplates('', document.querySelector('[data-filter-category-active]').dataset.filterCategoryActive);
        });
    }
}

// Auto resize textarea
let autoResizeTextarea = () => {
    document.querySelectorAll('[data-auto-resize-textarea]').forEach(function (element) {
        element.style.boxSizing = 'border-box';
        let offset = element.offsetHeight - element.clientHeight;

        // Resize the textarea
        element.addEventListener('input', function (event) {
            event.target.style.height = 'auto';
            event.target.style.height = event.target.scrollHeight + offset + 'px';
        });

        // Size the textarea
        element.style.height = element.scrollHeight + offset + 'px';

        element.removeAttribute('data-autoresize');
    });
};

autoResizeTextarea();

/**
 * Load the editor.
 */
let loadEditor = () => {
    // Get all the text editors
    document.querySelectorAll('[data-text-editor]').forEach(function (element) {
        let id = element.dataset.textEditor;
        let readOnly = element.dataset.textEditorReadonly === 'true';

        let container = document.getElementById('result-' + id);
        let options = {
            modules: {
                toolbar: '#toolbar-' + id
            },
            theme: 'snow',
            readOnly: readOnly,
        };

        let editor = new Quill(container, options);

        editor.on('text-change', function () {
            document.querySelector('#i-result-' + id) ? document.querySelector('#i-result-' + id).value = editor.root.innerHTML.trim() : '';
        });
    });
}

loadEditor();

if (document.querySelector('#form-chat')) {
    /**
     * Scroll the chat window to the end of the conversation.
     */
    let scrollChatWindowToEnd = () => {
        document.querySelector('#chat-container').scrollTop = document.querySelector('#chat-container').scrollHeight;
    }

    /**
     * Focus the chat message input.
     */
    let focusChatMessageInput = () => {
        document.querySelector('#i-message').focus();
    }

    /**
     * Clear the chat message input.
     */
    let clearChatMessageInput = () => {
        document.querySelector('#i-message').value = '';
    }

    /**
     * Disable the chat message input.
     */
    let disableChatMessageInput = () => {
        document.querySelector('#i-message').setAttribute('disabled', 'disabled');
    }

    /**
     * Enable the chat message input.
     */
    let enableChatMessageInput = () => {
        document.querySelector('#i-message').removeAttribute('disabled');
    }

    /**
     * Add the chat message input error.
     * @param message
     */
    let addChatMessageInputError = (message) => {
        document.querySelector('#i-message').classList.add('is-invalid');
        document.querySelector('.invalid-feedback strong').innerText = message;
    }

    /**
     * Clear the chat message input error.
     */
    let clearChatMessageInputError = () => {
        document.querySelector('#i-message').classList.remove('is-invalid');
    }

    /**
     * Restore a disabled button.
     */
    let restoreDisabledButton = (element) => {
        element.classList.remove('disabled');
        element.querySelector('.spinner-border').classList.add('d-none');
        element.querySelector('.spinner-text').classList.remove('invisible');
    }

    // Slight delay needed to prevent incomplete scrolling when elements are still being rendered
    setTimeout(function () {
        scrollChatWindowToEnd();
    }, 100);

    focusChatMessageInput();

    document.querySelector('#form-chat').addEventListener('submit', function (e) {
        e.preventDefault();

        chatMessage();
    });

    /**
     * Handle the AI Message.
     */
    let chatMessage = () => {
        clearChatMessageInputError();

        // If there's no chat message being sent
        if (!document.querySelector('#i-message').value) {
            focusChatMessageInput();
            restoreDisabledButton(document.querySelector('#form-chat button[type="submit"]'));

            return;
        }

        // Get the chat form
        const chatForm = new FormData(document.querySelector('#form-chat'));

        disableChatMessageInput();

        chatForm.set('role', 'user');
        let userRequestPromise = fetch(document.querySelector('#form-chat').getAttribute('action'), {
            method: 'post',
            headers: {
                "Accept": "application/json, text/javascript; charset=utf-8",
                "Content-Type": "application/json, text/javascript; charset=utf-8"
            },
            body: JSON.stringify(Object.fromEntries(chatForm))
        })
            .then(res => res.json())
            .then(response => {
                // If there are validation errors
                if (response.errors) {
                    addChatMessageInputError(response.errors.message[0]);

                    enableChatMessageInput();
                    restoreDisabledButton(document.querySelector('#form-chat button[type="submit"]'));

                    return false;
                }

                // Add the response to the chat messages list
                document.querySelector('#chat-messages').innerHTML += response.message;

                scrollChatWindowToEnd();

                // Rebind the modal event
                document.querySelectorAll('#chat-messages a[data-toggle="modal"]').forEach(function (element) {
                    confirmationModalEvent(element);
                });

                // Rebind the tooltip event
                jQuery('#chat-messages [data-tooltip="true"]').tooltip({ animation: true, trigger: 'hover', boundary: 'window' });

                chatForm.set('role', 'assistant');
                let assistantRequestPromise = fetch(document.querySelector('#form-chat').getAttribute('action'), {
                    method: 'post',
                    headers: {
                        "Accept": "application/json, text/javascript; charset=utf-8",
                        "Content-Type": "application/json, text/javascript; charset=utf-8"
                    },
                    body: JSON.stringify(Object.fromEntries(chatForm))
                })
                    .then(res => res.json())
                    .then(response => {
                        // If there are validation errors
                        if (response.errors) {
                            addChatMessageInputError(response.errors.message[0]);

                            enableChatMessageInput();
                            restoreDisabledButton(document.querySelector('#form-chat button[type="submit"]'));

                            return false;
                        }

                        // Add the response to the chat messages list
                        document.querySelector('#chat-messages').innerHTML += response.message;

                        scrollChatWindowToEnd();
                        focusChatMessageInput();
                        clearChatMessageInput();
                        enableChatMessageInput();
                        restoreDisabledButton(document.querySelector('#form-chat button[type="submit"]'));

                        // Rebind the modal event
                        document.querySelectorAll('#chat-messages a[data-toggle="modal"]').forEach(function (element) {
                            confirmationModalEvent(element);
                        });

                        // Rebind the tooltip event
                        jQuery('#chat-messages [data-tooltip="true"]').tooltip({ animation: true, trigger: 'hover', boundary: 'window' });
                    })
                    .catch(err => {
                        console.log(err);

                        focusChatMessageInput();
                        clearChatMessageInput();
                        enableChatMessageInput();
                        restoreDisabledButton(document.querySelector('#form-chat button[type="submit"]'));
                    });
            })
            .catch(err => {
                console.log(err);

                focusChatMessageInput();
                clearChatMessageInput();
                enableChatMessageInput();
                restoreDisabledButton(document.querySelector('#form-chat button[type="submit"]'));
            });
    }
}

/**
 * Get the value of a given cookie.
 *
 * @param   name
 * @returns {*}
 */
let getCookie = (name) => {
    var name = name + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');

    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
};

/**
 * Set a cookie.
 *
 * @param   name
 * @param   value
 * @param   expire
 * @param   path
 */
let setCookie = (name, value, expire, path) => {
    document.cookie = name + "=" + value + ";expires=" + (new Date(expire).toUTCString()) + ";path=" + path;
};
