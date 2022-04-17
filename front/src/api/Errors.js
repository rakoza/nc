class Errors {
    /**
     * Create a new Errors instance.
     */
    constructor() {
        this.errors = {};
    }


    /**
     * Determine if an errors exists for the given field.
     *
     * @param {string} field
     */
    has(field) {
        // return this.errors.hasOwnProperty(field);

        /**
         * ovako smo sposobni prikazati validacione greske koje dobijamo iz validacije elemenata niza
         * kad server vraca greske u kljucevima tipe dates.0, dates.1, itd
         */
        let hasError = false;
        Object.keys(this.errors).forEach(function(key) {
            if((key == field) || key.startsWith(field+'.')) {
                hasError = true;
            }
        });

        return hasError;
    }


    /**
     * Determine if we have any errors.
     */
    any() {
        return Object.keys(this.errors).length > 0;
    }

    /**
     * Get number of errors
     */
    count() {
        return Object.keys(this.errors).length;
    }

    /**
     * Get errors object keys. It is array
     */
    keys() {
        return Object.keys(this.errors);
    }

    /**
     * Retrieve the error message for a field.
     *
     * @param {string} field
     */
    get(field) {
        // if (this.errors[field]) {
        //     return this.errors[field][0];
        // }

        /**
         * ovako smo sposobni prikazati validacione greske koje dobijamo iz validacije elemenata niza
         * kad server vraca greske u kljucevima tipe dates.0, dates.1, itd
         */
        let errorMessages = [];
        Object.keys(this.errors).forEach((key) => {
            if((key == field) || key.startsWith(field+'.')) {
                errorMessages.push(this.errors[key][0]);
            }
        });

        return errorMessages.join('<br/>');
    }


    /**
     * Record the new errors.
     *
     * @param {object} errors
     */
    record(errors) {
        this.errors = errors;
    }


    /**
     * Clear one or all error fields.
     *
     * @param {string|null} field
     */
    clear(field) {
        if (field) {
            delete this.errors[field];

            return;
        }

        this.errors = {};
    }
}

export default Errors;
