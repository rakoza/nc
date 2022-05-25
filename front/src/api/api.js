import axios from 'axios'

// https://dev.to/kevinleedrum/axios-tips-for-real-world-apps-3bo4
// https://github.com/axios/axios
const http = axios.create({

    // `withCredentials` indicates whether or not cross-site Access-Control requests
    // should be made using credentials
    // withCredentials: true,

    // `baseURL` will be prepended to `url` unless `url` is absolute.
    // It can be convenient to set `baseURL` for an instance of axios to pass relative URLs
    // to methods of that instance.
    // baseURL: '/spa',
    baseURL: '/spa',
})

const successHandler = (response) => {
    return response.data
}

const errorHandler = (error) => {
    // in case of error.message == 'Network Error', error.response is undefined
    if(error.response) {
        // Add response interceptor to AXIOS instance for automatic forward
        // to login page in case of session expiration.
        if (error.response.status == 401 || error.response.data.exception == "Illuminate\\Session\\TokenMismatchException") {
            window.location = '/login';
        }

        // Show custom error message if we have one, ex. abort(403, 'Operation is not allowed')
        if (error.response.data.message) {
            return Promise.reject(new Error(error.response.data.message))
        }
    }

    return Promise.reject(error)
}

// axios interceptors
http.interceptors.response.use(
    // https://dev.to/teroauralinna/global-http-request-and-response-handling-with-the-axios-interceptor-30ae
    response => successHandler(response),
    error => errorHandler(error)
);


/* ---------------------------------------------------------
 * API routes
 * ---------------------------------------------------------
 */
const app = {
    // checkSession: () => http.get('/check-session'),
    logout: () => http.post('/logout'),
    upload: (formData) => http.post('/upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        },
    }),
    config: () => http.get('app/config'),
    // getVersions: () => http.get('app/versions'),
    // getTimezones: () => http.get('app/timezones'),
}

const profile = {
    get: () => http.get('/profile'),
    delete: () => http.delete('/profile'),
}

const users = {
    all: () => http.get('users')
}

const tenants = {
    all: () => http.get('tenants'),
    get: (id) => http.get(`tenants/${id}`),
    delete: (id) => http.delete(`tenants/${id}`),
}

const docker = {
    status: () => http.get('docker/status'),
    nginx: () => http.get(`docker/nginx`),
    containers: () => http.get(`docker/containers`),
    tenant: (id) => http.get(`docker/container/${id}`),
    startContainer: (containerId) => http.post(`docker/container/${containerId}/start`),
    stopContainer: (containerId) => http.post(`docker/container/${containerId}/stop`),
    removeContainer: (containerId) => http.delete(`docker/container/${containerId}/remove`),
}

/* ---------------------------------------------------------
 * JS Module export
 * ---------------------------------------------------------
 */
export default {
    app,
    profile,
    users,
    tenants,
    docker,
}
