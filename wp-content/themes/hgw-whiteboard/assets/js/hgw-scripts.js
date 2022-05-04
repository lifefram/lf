/*
*
*   Show ToggleButton in mobile
*
*/
/////////////////////////////////////////////////////////////
//HIDING ALL OTHER CONTENT FROM SCREEN READERS
var content = document.getElementById('site-content');
var menuBtn = document.querySelector('.open-menu');
var closeMenuBtn = document.querySelector('.close-menu');
var menu = document.querySelector('.pas');
var focusableItems = [
  'a[href]',
  'area[href]',
  'input:not([disabled])',
  'select:not([disabled])',
  'textarea:not([disabled])',
  'button:not([disabled])',
  '[tabindex]:not([disabled])',
  '[contenteditable=true]:not([disabled])'
];

//the main function for setting the tabindex to -1 for all children of a parent with given ID (and reversing the process)
function hideOrShowAllInteractiveItems(parentDivID){

  //build a query string that targets the parent div ID and all children elements that are in our focusable items list.
  var queryString = "";
  for (i = 0, leni = focusableItems.length; i < leni; i++) {
    queryString += "#" + parentDivID + " " + focusableItems[i] + ", ";
  }
  queryString = queryString.replace(/,\s*$/, "");

  var focusableElements = document.querySelectorAll(queryString);
  for (j = 0, lenj = focusableElements.length; j < lenj; j++) {

    var el = focusableElements[j];
    if(!el.hasAttribute('data-modified')){ // we use the 'data-modified' attribute to track all items that we have applied a tabindex to (as we can't use tabindex itself).

      // we haven't modified this element so we grab the tabindex if it has one and store it for use later when we want to restore.
      if(el.hasAttribute('tabindex')){
        el.setAttribute('data-oldTabIndex', el.getAttribute('tabindex'));
      }

      el.setAttribute('data-modified', true);
      el.setAttribute('tabindex', '-1'); // add `tabindex="-1"` to all items to remove them from the focus order.

    }else{
      //we have modified this item so we want to revert it back to the original state it was in.
      el.removeAttribute('tabindex');
      if(el.hasAttribute('data-oldtabindex')){
        el.setAttribute('tabindex', el.getAttribute('data-oldtabindex'));
        el.removeAttribute('data-oldtabindex');
      }
      el.removeAttribute('data-modified');
    }
  }
}


var globalVars = {};


function openMenu(){
     menu.classList.add("open");
     menuBtn.setAttribute('aria-expanded', true);

     //get all the focusable items in our menu and keep track of the button that opened the menu for when we close it again.
     setFocus(menuBtn, 'pas');

     content.setAttribute("aria-hidden", true);
}

function closeMenu(){
    //close menu
    //unhide the main content
    content.setAttribute("aria-hidden", false);
    //hide the menu
     menu.classList.remove("open");
     // set `aria-expanded` - important for screen reader users.
     menuBtn.setAttribute('aria-expanded', false);
     //set focus back to the button that opened the menu if we can
     if (globalVars.beforeOpen) {
        globalVars.beforeOpen.focus();
     }
}




//toggle the menu
menuBtn.addEventListener('click', function(){
  //use our function to add the relevant `tabindex="-1"` to all interactive elements outside of the menu.
  hideOrShowAllInteractiveItems('site-content');
  //check if the menu is open, if it is close it and reverse everything.
  openMenu();
});

closeMenuBtn.addEventListener('click', function(){
  //use our function to add the relevant `tabindex="-1"` to all interactive elements outside of the menu.
  hideOrShowAllInteractiveItems('site-content');
  //check if the menu is open, if it is close it and reverse everything.
  closeMenu();
});

/////////////////////////////////////////////////////////////
//TRAPPING FOCUS



var setFocus = function (item, className) { //we pass in the button that activated the menu and the className of the menu list, your menu must have a unique className for this to work.

    className = className || "content"; //defaults to class 'content' in case of error ("content" being the class on the <main> element.)
    globalVars.beforeOpen = item; //we store the button that was pressed before the modal opened in a global variable so we can return focus to it on modal close.

    var findItems = [];
    for (i = 0, len = focusableItems.length; i < len; i++) {
        findItems.push('.' + className + " " + focusableItems[i]); //add every focusable item to an array.
    }
    // finally add the open button to our list of focusable items as it sits outside our menu list.



    var findString = findItems.join(", ");
    globalVars.canFocus = Array.prototype.slice.call(document.querySelectorAll(findString));
    if (globalVars.canFocus.length > 0) {
        globalVars.canFocus[0].focus(); //***set the focus to the first focusable element within the modal
        globalVars.lastItem = globalVars.canFocus[globalVars.canFocus.length - 1]; //we also store the last focusable item within the modal so we can keep focus within the modal.
    }
}

//listen for keypresses and intercept both the Esc key (to close the menu) and tab and shift tab while the menu is open so we can manage focus.
document.onkeydown = function (evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        //unhide the main content - exactly the same as in the btn event listener.
     hideOrShowAllInteractiveItems('site-content');
     closeMenu();
    }
  if (menu.classList.contains('open') && evt.keyCode == 9) { //global variable to check any modal is open and key is the tab key
        if (evt.shiftKey) { //also pressing shift key
            if (document.activeElement == globalVars.canFocus[0]) { //the current element is the same as the first focusable element
                evt.preventDefault();
                globalVars.lastItem.focus(); //we focus the last focusable element as we are reverse tabbing through the items.
            }
        } else {
            if (document.activeElement == globalVars.lastItem) { //when tabbing forward we look for the last tabbable element
                evt.preventDefault();

                globalVars.canFocus[0].focus(); //move the focus to the first tabbable element.
            }
        }
    }
};

// End Menu
/////////////////////////////////////////////////////////////

/*
*
*   Copy Shortlink in singular Page
*
*/

const textToCopy = document.getElementById("shorturl")
const copy   = document.getElementById("copyshorturl");
const answer = document.getElementById("copyresultshorturl");
const selection = window.getSelection();
const range = document.createRange();

copy.addEventListener('click', function(e) {
    range.selectNodeContents(textToCopy);
    selection.removeAllRanges();
    selection.addRange(range);
    const successful = document.execCommand('copy');
    if(successful){
      answer.innerHTML = 'Copied';
    } else {
      answer.innerHTML = 'Error';
    }
    window.getSelection().removeAllRanges()
});
// End Shortlink Copy Button
/////////////////////////////////////////////////////////////
