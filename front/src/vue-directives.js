import Vue from 'vue';

/* ---------------------------------------------------------
 * Global VueJS directives
 * ---------------------------------------------------------
 */
Vue.directive('numericOnly', {
  // https://stackoverflow.com/questions/48700683/input-field-only-accepting-numeric-value-using-directive-in-vue-js
  bind(el) {
      el.addEventListener('keydown', (e) => {
            // console.log(e.target.value)

          if ([46, 8, 9, 27, 13, 110, 190, 109].indexOf(e.keyCode) !== -1 ||
              // Allow: Ctrl+A
              (e.keyCode === 65 && e.ctrlKey === true) ||
              // Allow: Ctrl+C
              (e.keyCode === 67 && e.ctrlKey === true) ||
              // Allow: Ctrl+X
              (e.keyCode === 88 && e.ctrlKey === true) ||
              // Allow: comma
              (e.keyCode === 188) ||
              // Allow: home, end, left, right
              (e.keyCode >= 35 && e.keyCode <= 39)) {
              // let it happen, don't do anything
              return
          }
          // Ensure that it is a number and stop the keypress
          if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
              e.preventDefault()
          }
      })

      el.addEventListener('keyup', (e) => {
          //https://bobbyhadz.com/blog/javascript-replace-comma-with-dot
          e.target.value = e.target.value.replaceAll(',', '.')
      })
  }
})
