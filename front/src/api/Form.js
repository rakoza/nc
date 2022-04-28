import Errors from './Errors';
import axios from 'axios';
import { ToastProgrammatic as Toast } from 'buefy';
import moment from 'moment';

class Form {

    /**
     * Form podaci definisano prilikom inicijalizacije forme
     * @type {Object}
     */
    #originalData = {};

    /**
     * U Errors objekt smjestamo validacione greske ako postoje
     *
     * Vazno je da bude javno vidljiva, kako bi imali reaktivnost u vue komponentama (formi)
     * u slucaju kad dodje do izmjene u ovom objektu
     *
     * @type {Errors}
     */
    errors = new Errors();

    /**
     * Convert data to be submitted to FormData object
     * Need for file uploads, when Content-type is set as multipart/form-data
     * @type {Boolean}
     */
    #passAsFormData = false;

    /**
     * https://dev.to/kevinleedrum/axios-tips-for-real-world-apps-3bo4
     * https://github.com/axios/axios
     * @type {Function}
     */
    http = axios.create({
        // `withCredentials` indicates whether or not cross-site Access-Control requests
        // should be made using credentials
        // withCredentials: true,

        // `baseURL` will be prepended to `url` unless `url` is absolute.
        // It can be convenient to set `baseURL` for an instance of axios to pass relative URLs
        // to methods of that instance.
        baseURL: '/spa',
    });

    /**
     * Form constructor class
     *
     * @param {Object} data
     */
    constructor(data) {
        this.setData(data)

        // https://github.com/axios/axios#interceptors
        // You can intercept requests or responses before they are handled by then or catch.
        this.http.interceptors.response.use(
            response => {
                return response
            },
            error => {
                // in case of error.message == 'Network Error', error.response is undefined
                if(error.response) {
                    // Add response interceptor to AXIOS instance for automatic forward
                    // to login page in case of session expiration.
                    if (error.response.status == 401 || error.response.data.exception == "Illuminate\\Session\\TokenMismatchException") {
                        window.location = '/login';
                    }
                }

                return Promise.reject(error)
            }
        );
    }

    /**
     * Set form data
     *
     * @param {Object} data
     */
    setData(data) {
        this.#originalData = data;

        for(let field in data) {

            let value = data[field]

            // formatiramo u Date object, prilagodjavamo za Buefy date picker
            if(moment(value, "YYYY-MM-DD", true).isValid()) {
                value = moment(value).toDate()
            }

            this[field] = value
        }
    }

    /**
     * Get back original data
     */
    reset() {
        for(let field in this.#originalData) {
            this[field] = this.#originalData[field];
        }

        this.errors = new Errors();
    }

    /**
     * Object to be submitted via http request
     *
     * @return {Object}
     */
    #data() {
        const data = {}

        for (let [key, value] of Object.entries(this)) {
            // private properties are not to be listed

            // functions (http) are to be skipped
            if(typeof value === 'function' || key === 'errors') {
                continue
            }

            // formatiramo u date string, jer Buefy date picker radi sa Date objektom
            if(value instanceof Date) {
                value = moment(value).format("YYYY-MM-DD")
            }

            data[key] = value
        }

        // console.log(data)

        return data;
    }

    /**
     * Call if upload files
     */
    passAsFormData() {
        this.#passAsFormData = true
    }

    // https://developer.mozilla.org/en-US/docs/Web/API/FormData
    // The FormData interface provides a way to easily construct
    // a set of key/value pairs representing form fields and their values.
    // It uses the same format a form would use if the encoding type were set to "multipart/form-data".
    transformToFormData() {
        let formData = new FormData();
        let data = this.#data()

        for ( const key in data ) {

            const value = data[key]

            if(Array.isArray(value)) {
                // ovo je slucaj kad saljemo niz elemenata
                // npr kad saljemo niz slika, multiple upload
                value.forEach(item => {
                    formData.append(key+'[]', item);
                })
            } else {
                formData.append(key, value === null ? '' : value);
            }
        }

        return formData;
    }

    post(url) {
        return this.submit('post', url)
    }

    put(url) {
        return this.submit('put', url)
    }

    patch(url) {
        return this.submit('patch', url)
    }

    delete(url) {
        return this.submit('delete', url)
    }

    /**
     * Callback method - TO BE DEFINED DYNAMICALLY
     *
     * @return {[type]}          [description]
     */
    onSuccessCallback() {}

    /**
     * Callback method - TO BE DEFINED DYNAMICALLY
     *
     * @return {[type]} [description]
     */
    onErrorCallback() {}

    /**
     * Subit the form data to the specified url
     *
     * @return {Promise}
     */
    submit(method, url) {
        let data = this.#data()

        if(method === 'create') {
            method = 'post'
        }

        if(method === 'update' || method === 'edit') {
            method = 'put'
        }

        if (this.#passAsFormData) {
            data = this.transformToFormData();

            if (method == 'put') {
                method = 'post';
                data.append('_method', 'PUT');
            }
        }

        const request = {
            method,
            url,
            // `data` is the data to be sent as the request body
            // Only applicable for request methods 'PUT', 'POST', 'DELETE , and 'PATCH'
            // When no `transformRequest` is set, must be of one of the following types:
            // - string, plain object, ArrayBuffer, ArrayBufferView, URLSearchParams
            // - Browser only: FormData, File, Blob
            // - Node only: Stream, Buffer
            data
        }

        return new Promise((resolve, reject) => {
            this.http.request(request)
                .then(response => {
                    const data = this.onSuccess(response);
                    resolve(data);

                    this.onSuccessCallback(response)
                })
                .catch(error => {
                    this.onError(error);
                    // console.log(error.response)
                    reject(error);

                    this.onErrorCallback(error)
                });
        });
    }

    /**
     * On form success return data
     *
     * @param  {Object} response
     * @return {Object}
     */
    onSuccess(response) {
        const data = response.data;

        this.errors.clear();

        return data;
    }

    /**
     * @param  {Object} error
     *
     * On form failed record errors to the errors object
     */
    onError(error) {
        const response = error.response
        const errors = response.data.errors

        // status 422 is for laravel validation errors
        if(response.status !== 422) {
            this.notify(response.data.message, 'is-danger');
        }

        // laravel validation errors array
        if(errors != undefined) {
            this.errors.record(errors);
        }
    }

    /**
     * Clear the field error in Errors array
     */
    clearField(event)
    {
        // console.log(event)
        if(event.target) {
            // in this case we get input name from form event object
            // doesn't work for datepickers, then we use else
            this.errors.clear(event.target.name)
        } else {
            // in this case we get field event as a string
            // for datepickers
            this.errors.clear(event)
        }
    }

    //------------------------------------------------------------
    // To use in Buefy inputs and other form elements
    hasError(field) {
        return this.errors.has(field) ? 'is-danger' : '';
    }

    errorMessage(field) {
        return this.errors.get(field);
    }

    notify(text, type) {
        Toast.open({
            message: text,
            type: type,
            queue: false,
            duration: 5000,
        })
    }
}

export default Form;
