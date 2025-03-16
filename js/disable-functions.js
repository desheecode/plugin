/**
 * Main Security Functions Module
 * यह फाइल वेबसाइट की सुरक्षा के लिए विभिन्न फंक्शन्स को नियंत्रित करती है
 */

(function($) {
    'use strict';

    // सभी सुरक्षा फंक्शन्स का मुख्य ऑब्जेक्ट
    const SecurityFeatures = {
        /**
         * राइट क्लिक को डिसेबल करता है
         * @param {Event} e - इवेंट ऑब्जेक्ट
         */
        disableRightClick: function(e) {
            e.preventDefault();
            return false;
        },

        /**
         * टेक्स्ट सिलेक्शन को डिसेबल करता है
         * @param {Event} e - इवेंट ऑब्जेक्ट
         */
        disableTextSelection: function(e) {
            e.preventDefault();
            return false;
        },

        /**
         * इमेज डाउनलोड को डिसेबल करता है
         * @param {Event} e - इवेंट ऑब्जेक्ट
         */
        disableImageDownload: function(e) {
            e.preventDefault();
            return false;
        },

        /**
         * कीबोर्ड शॉर्टकट को डिसेबल करता है
         * @param {Event} e - इवेंट ऑब्जेक्ट
         */
        disableKeyboardShortcuts: function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (disableFunctions.ctrlS === 'on' && (e.key === 's' || e.key === 'S')) {
                    e.preventDefault();
                    return false;
                }
                if (disableFunctions.ctrlU === 'on' && (e.key === 'u' || e.key === 'U')) {
                    e.preventDefault();
                    return false;
                }
                if (disableFunctions.ctrlP === 'on' && (e.key === 'p' || e.key === 'P')) {
                    e.preventDefault();
                    return false;
                }
            }
        },

        /**
         * वॉटरमार्क जोड़ता है
         * @param {string} text - वॉटरमार्क का टेक्स्ट
         */
        addWatermark: function(text) {
            const watermark = $('<div/>', {
                class: 'content-watermark',
                text: text
            });
            $('body').append(watermark);
        }
    };

    /**
     * नए सुरक्षा फीचर्स को जोड़ने के लिए एक्सटेंशन फंक्शन
     * @param {string} name - फीचर का नाम
     * @param {Function} handler - फीचर का हैंडलर फंक्शन
     */
    SecurityFeatures.addNewFeature = function(name, handler) {
        if (typeof handler === 'function') {
            this[name] = handler;
        }
    };

    // सभी फीचर्स को इनिशियलाइज करें
    jQuery(document).ready(function($) {
        'use strict';

        // एरर मैसेज दिखाने का फंक्शन
        function showErrorMessage(message) {
            if (disableFunctions.showErrors === 'on') {
                const position = disableFunctions.errorStyle.position;
                const distance = parseInt(disableFunctions.errorStyle.distance);

                // पोजीशन के हिसाब से CSS प्रॉपर्टीज
                const positionCSS = {
                    position: 'fixed',
                    left: '50%',
                    transform: 'translateX(-50%)',
                    zIndex: 999999,
                    boxShadow: '0 0 10px rgba(0,0,0,0.5)',
                    background: disableFunctions.errorStyle.background,
                    color: disableFunctions.errorStyle.textColor,
                    padding: `${disableFunctions.errorStyle.padding}px`,
                    borderRadius: `${disableFunctions.errorStyle.borderRadius}px`,
                    fontSize: `${disableFunctions.errorStyle.fontSize}px`,
                    textAlign: 'center',
                    fontFamily: 'Arial, sans-serif',
                    minWidth: '200px',
                    maxWidth: '80%',
                    wordWrap: 'break-word',
                    opacity: 0,
                    transition: 'opacity 0.3s ease-in-out'
                };

                // पोजीशन के अनुसार टॉप/बॉटम सेट करें
                switch (position) {
                    case 'top':
                        positionCSS.top = `${distance}px`;
                        positionCSS.transform += ' translateY(0)';
                        break;
                    case 'bottom':
                        positionCSS.bottom = `${distance}px`;
                        positionCSS.transform += ' translateY(0)';
                        break;
                    default: // center
                        positionCSS.top = '50%';
                        positionCSS.transform += ' translateY(-50%)';
                        break;
                }

                const errorDiv = $('<div/>', {
                    class: 'security-error-message',
                    text: message
                }).css(positionCSS);

                $('body').append(errorDiv);

                // फेड इन एनिमेशन
                setTimeout(() => {
                    errorDiv.css('opacity', '1');
                }, 10);

                // फेड आउट और रिमूव
                setTimeout(function() {
                    errorDiv.css('opacity', '0');
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 300);
                }, parseInt(disableFunctions.errorStyle.duration));
            }
        }

        // Ctrl+C को रोकें
        if (disableFunctions.ctrlC === 'on') {
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C' || e.keyCode === 67)) {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.copy);
                    return false;
                }
            });

            // कॉपी इवेंट को रोकें
            $(document).on('copy', function(e) {
                if (disableFunctions.ctrlC === 'on') {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.copy);
                    return false;
                }
            });
        }

        // Ctrl+Shift+I को रोकें
        if (disableFunctions.ctrlShiftI === 'on') {
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'i' || e.key === 'I' || e.keyCode === 73)) {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.devTools);
                    return false;
                }
            });
        }

        // स्क्रीनशॉट को रोकें
        if (disableFunctions.printScreen === 'on') {
            // PrtScr कुंजी को रोकें
            $(document).on('keydown', function(e) {
                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.printScreen);
                    return false;
                }
            });

            // स्क्रीनशॉट रोकने के लिए अतिरिक्त सुरक्षा
            document.addEventListener('keyup', function(e) {
                if (e.key === 'PrintScreen' || e.keyCode === 44) {
                    navigator.clipboard.writeText('');
                    showErrorMessage(disableFunctions.messages.printScreen);
                }
            });

            // कैप्चर API को रोकें
            navigator.mediaDevices.getDisplayMedia = function() {
                return new Promise((resolve, reject) => {
                    showErrorMessage(disableFunctions.messages.printScreen);
                    reject(new Error('स्क्रीन कैप्चर डिसेबल है'));
                });
            };
        }

        // F12 डेवलपर टूल्स को रोकें
        if (disableFunctions.f12 === 'on') {
            $(document).on('keydown', function(e) {
                if (e.key === 'F12' || e.keyCode === 123) {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.devTools);
                    return false;
                }
            });

            // डेवटूल्स को रोकने के लिए अतिरिक्त चेक
            setInterval(function() {
                const devtools = /./;
                devtools.toString = function() {
                    if (disableFunctions.f12 === 'on') {
                        showErrorMessage(disableFunctions.messages.devTools);
                        window.location.reload();
                    }
                    return '';
                }
                console.log('%c', devtools);
            }, 1000);
        }

        // राइट क्लिक डिसेबल
        if (disableFunctions.rightClick === 'on') {
            $(document).on('contextmenu', function(e) {
                e.preventDefault();
                showErrorMessage(disableFunctions.messages.rightClick);
                return false;
            });
        }

        // टेक्स्ट सिलेक्शन डिसेबल
        if (disableFunctions.textSelection === 'on') {
            $(document).on('selectstart', function(e) {
                e.preventDefault();
                return false;
            });
        }

        // इमेज डाउनलोड डिसेबल
        if (disableFunctions.imageDownload === 'on') {
            $('img').on('contextmenu dragstart', function(e) {
                e.preventDefault();
                showErrorMessage(disableFunctions.messages.rightClick);
                return false;
            });
        }

        // कीबोर्ड शॉर्टकट डिसेबल
        $(document).on('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (disableFunctions.ctrlS === 'on' && (e.key === 's' || e.key === 'S')) {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.devTools);
                    return false;
                }
                if (disableFunctions.ctrlU === 'on' && (e.key === 'u' || e.key === 'U')) {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.devTools);
                    return false;
                }
                if (disableFunctions.ctrlP === 'on' && (e.key === 'p' || e.key === 'P')) {
                    e.preventDefault();
                    showErrorMessage(disableFunctions.messages.devTools);
                    return false;
                }
            }
        });

        // वॉटरमार्क जोड़ें
        if (disableFunctions.watermark === 'on') {
            const watermark = $('<div/>', {
                class: 'content-watermark',
                text: disableFunctions.watermarkText
            });
            $('body').append(watermark);
        }
    });

    // SecurityFeatures को ग्लोबल स्कोप में एक्सपोर्ट करें ताकि अन्य स्क्रिप्ट्स इसका उपयोग कर सकें
    window.SecurityFeatures = SecurityFeatures;

})(jQuery);
