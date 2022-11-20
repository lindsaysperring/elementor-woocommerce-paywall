(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  $(document).ready(function () {
    $("#post_type_multiselect").multiselect({
      nonSelectedText: "Select Framework",
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true,
      buttonWidth: "400px",
    });

    $("#product_select_button").click(function () {
      let id, value;
      const selected_product = $("#product_select :selected");
      id = selected_product.val();
      value = selected_product.text();
      console.log(posts);
      let select_string = `
	  <div id="ep_settings_product_post_link_wrapper_${id}">
	  <label htmlFor="${id}">${value}</label>
	  <select name="ep_settings_product_post_link[${id}][]" id="ep_settings_product_post_link_${id}" multiple class="select" style="display:inline;">
	  `;

      posts.forEach((post) => {
        select_string += `<option value="${post.ID}">${post.post_title}</option>`;
      });

      select_string += `</select><span onclick="deleteInput(${id})" style="cursor:pointer;">&#x2715;</span></div>`;

      $("#post-product-select").append(select_string);

      $(`#ep_settings_product_post_link_${id}`).multiselect({
        nonSelectedText: "Select Framework",
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth: "400px",
      });
    });
  });
})(jQuery);

function deleteInput(inputId) {
  jQuery(`#ep_settings_product_post_link_wrapper_${inputId}`).remove();
}
