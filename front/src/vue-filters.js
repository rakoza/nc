import Vue from 'vue';
import moment from 'moment';

/* ---------------------------------------------------------
 * VueJS filters
 * ---------------------------------------------------------
 */
Vue.filter('dd_mm_yyyy', function (dateString) {
    return !dateString ? '' : moment(dateString).format('DD.MM.YYYY');
})

Vue.filter('dd_mm_yyyy_h_i_s', function (dateString) {
    return !dateString ? '' : moment(dateString).format('DD.MM.YYYY HH:mm:ss');
})

Vue.filter('dd_mm', function (dateString) {
    return !dateString ? '' : moment(dateString).format('DD.MM');
})

Vue.filter('dd_mm_', function (dateString) {
    return !dateString ? '' : moment(dateString).format('DD.MM.');
})

Vue.filter('dd', function (dateString) {
    return !dateString ? '' : moment(dateString).format('DD');
})

Vue.filter('dd_mmm', function (dateString) {
    return !dateString ? '' : moment(dateString).format('DD.MMM');
})

Vue.filter('mmm_yy', function (dateString) {
    return !dateString ? '' : moment(dateString).format('MMM YY');
})

Vue.filter('yy', function (dateString) {
    return !dateString ? '' : moment(dateString).format('YY');
})

Vue.filter('mmm_yyyy', function (dateString) {
    return !dateString ? '' : moment(dateString).format('MMM YYYY');
})

Vue.filter('mm_yyyy', function (dateString) {
    return !dateString ? '' : moment(dateString).format('M.YYYY');
})

Vue.filter('h_i', function (dateString) {
    return !dateString ? '' : moment(dateString).format('HH:mm');
})

Vue.filter('dddd', function (dateString) {
    // Sunday Monday ... Friday Saturday
    return !dateString ? '' : moment(dateString).format('dddd');
})

Vue.filter('date', function (dateString) {
    // to Date object
    return !dateString ? null : moment(dateString).toDate();
})

Vue.filter('decimal', function (value) {
    return typeof value == 'number' ? value.toFixed(2) : value;
})

Vue.filter('currency', function (curr) {
  if (!curr || curr=='eur') return '€';
  if (curr=='hrk') return 'HRK';
})

Vue.filter('initials', function (nameSurname) {
    return !nameSurname ? null : nameSurname.split(' ').map(item => item.substring(0,1)).join('/')
})

Vue.filter('money', function (amount) {
    // https://stackoverflow.com/questions/149055/how-to-format-numbers-as-currency-string
    return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
})

Vue.filter('filesize', function (size) {
    const bytes = parseInt(size)
    if(bytes > 1024) {
        return (bytes / (1024*1024)).toFixed(2) + 'M'
    }
    return bytes + 'b'
})

Vue.filter('zero2null', function (value) {
    return value == 0 ? null : value;
})
