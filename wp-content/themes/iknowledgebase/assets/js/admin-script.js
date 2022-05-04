'use strict';

(function($) {

  $('.field-item-type select').each(function() {
    iknowledgeMenuType(this);
  });

  $('body').on('change', '.field-item-type select', function() {
    iknowledgeMenuType(this);
  });

  function iknowledgeMenuType(el) {
    const type = $(el).val();
    console.log(type);
    const parent = $(el).parents('.menu-item-settings');
    const btnType = $(parent).find('.field-item-type-button');
    $(btnType).hide();
    if (type === 'button') {
      $(btnType).show();
    }
  }

})(jQuery);


