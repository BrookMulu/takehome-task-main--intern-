'use strict';

( function () {
	// NOTE: Please do not use any third-party libraries to implement the
	// following as we want to keep the JS payload as small as possible. You may
	// use ES6. There is no need to support IE11.
	//
	// TODO A: Improve the readability of this file through refactoring and
	// documentation. Make any changes you think are necessary.
	//
	// TODO B: When typing in the "title" field, we want to auto-complete based on
	// article titles that already exist. You may use the
	// api.php?prefixsearch={search} endpoint for auto-completion. To avoid
	// hitting the server endpoint excessively, please also add JavaScript code
	// that ensures at least 200ms has passed between requests. Check the
	// `design-spec/auto-complete-hover.png` file for the design spec.
	// Also, you don't need to make the autocomplete list disappear when the input
	// has lost focus in this TODO. That will be handled as part of TODO D.
	//
	// TODO C: When the user selects an item from the auto-complete list, we want
	// the textarea to populate with that article's contents. You may use the
	// api.php?title={title} endpoint to get the article's contents. Check the
	// `design-spec/auto-complete-select.png` file for the design spec.
	//
	// TODO D: The autocomplete list should only be shown when the input receives
	// focus. The list should be hidden after the user selects an item from the
	// list or after the input loses focus.
	//
	// TODO F: Add error-handling requirements, such as displaying error messages
	// to the user when API requests fail and provide a graceful degradation of
	// functionality.

	/**
     * Attaches a click event to the submit button to submit the form.
     */
    function enableFormSubmission() {
        const submitButton = document.querySelector('.submit-button');
        const form = document.querySelector('form');

        if (!submitButton || !form) {
            console.warn('Submit button or form not found.');
            return;
        }

        submitButton.addEventListener('click', (event) => {
            event.preventDefault();
            form.submit();
        });
    }

    /**
     * Initializes the autocomplete functionality for the title input field.
     */
    function initializeAutocomplete() {
        const titleInput = document.querySelector('input[name="title"]');
        const autocompleteList = document.createElement('ul');
        autocompleteList.classList.add('autocomplete-list');
        titleInput.parentNode.appendChild(autocompleteList);

        let debounceTimeout;

        /**
         * Fetch suggestions from the server based on user input.
         * @param {string} query - The user's input value.
         * @return {Promise<Array<string>>} - Array of suggestions from the server.
         */
        async function fetchSuggestions(query) {
            try {
                const response = await fetch(`api.php?prefixsearch=${encodeURIComponent(query)}`);
                if (!response.ok) throw new Error('Error fetching suggestions.');
                const data = await response.json();
                return data.content || [];
            } catch (error) {
                console.error('Autocomplete API error:', error);
                return [];
            }
        }

        /**
         * Renders the suggestions list under the title input.
         * @param {Array<string>} suggestions - List of suggestions from the server.
         */
        function renderSuggestions(suggestions) {
            autocompleteList.innerHTML = ''; 

            if (suggestions.length === 0) {
                autocompleteList.style.display = 'none';
                return;
            }

            suggestions.forEach((suggestion) => {
                const listItem = document.createElement('li');
                listItem.textContent = suggestion;
                listItem.addEventListener('click', () => {
                    titleInput.value = suggestion;
                    autocompleteList.style.display = 'none';
                });
                autocompleteList.appendChild(listItem);
            });

            autocompleteList.style.display = 'block';
        }

        /**
         * Handles user input with debounce to limit server requests.
         * @param {Event} event - Input event triggered by typing in the title field.
         */
        function handleInput(event) {
            const query = event.target.value.trim();

            if (!query) {
                autocompleteList.style.display = 'none';
                return;
            }

            clearTimeout(debounceTimeout);

            debounceTimeout = setTimeout(async () => {
                const suggestions = await fetchSuggestions(query);
                renderSuggestions(suggestions);
            }, 200); 
        }

        titleInput.addEventListener('input', handleInput);
    }

    /**
     * Waits for the page to load and then enables all functionality.
     */
    function initialize() {
        setTimeout(() => {
            enableFormSubmission();
            initializeAutocomplete();
        }, 1500);
    }
	
    initialize();
})();
