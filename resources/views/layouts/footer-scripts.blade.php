<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="fas fa-arrow-alt-circle-up"></i></a>
<!-- JQuery min js -->
<script src="{{URL::asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap Bundle js -->
<script src="{{URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Ionicons js -->
<script src="{{URL::asset('assets/plugins/ionicons/ionicons.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/moment/moment.js')}}"></script>

<!-- Rating js-->
<script src="{{URL::asset('assets/plugins/rating/jquery.rating-stars.js')}}"></script>
<script src="{{URL::asset('assets/plugins/rating/jquery.barrating.js')}}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/perfect-scrollbar/p-scroll.js')}}"></script>
<!--Internal Sparkline js -->
<script src="{{URL::asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Custom Scroll bar Js-->
<script src="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<!-- right-sidebar js -->
<script src="{{URL::asset('assets/plugins/sidebar/sidebar-rtl.js')}}"></script>
<script src="{{URL::asset('assets/plugins/sidebar/sidebar-custom.js')}}"></script>
<!-- Eva-icons js -->
<script src="{{URL::asset('assets/js/eva-icons.min.js')}}"></script>
@yield('js')
@yield('modals')
<script src="{{URL::asset('assets/js/theme-settings.js')}}"></script>
<!-- Sticky js -->
<script src="{{URL::asset('assets/js/sticky.js')}}"></script>
<!-- custom js -->
<script src="{{URL::asset('assets/js/custom.js')}}"></script><!-- Left-menu js-->
<script src="{{URL::asset('assets/plugins/side-menu/sidemenu.js')}}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;

        // Load dark mode preference from local storage
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-theme');
        }

        // Toggle dark mode on button click
        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (body.classList.contains('dark-theme')) {
                body.classList.remove('dark-theme');
                localStorage.setItem('darkMode', 'disabled');
            } else {
                body.classList.add('dark-theme');
                localStorage.setItem('darkMode', 'enabled');
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bgColorBoxes = document.querySelectorAll('.bg-color-box');
        const textColorBoxes = document.querySelectorAll('.text-color-box');

        function applyColor(elements, color, property) {
            elements.forEach(selector => {
                document.querySelectorAll(selector).forEach(el => {
                    el.style.setProperty(property, color, 'important');
                });
            });
        }

        function setActiveColorBox(colorBoxes, selectedColor) {
            colorBoxes.forEach(box => {
                box.classList.remove('active');
                if (box.dataset.color === selectedColor) {
                    box.classList.add('active');
                }
            });
        }

        bgColorBoxes.forEach(box => {
            box.addEventListener('click', function() {
                const color = this.dataset.color;
                const targets = this.dataset.target.split(', ');
                applyColor(targets, color, 'background-color');
                setActiveColorBox(bgColorBoxes, color);
                localStorage.setItem('selectedBgColor', color);
            });
        });

        textColorBoxes.forEach(box => {
            box.addEventListener('click', function() {
                const color = this.dataset.color;
                const targets = this.dataset.target.split(', ');
                applyColor(targets, color, 'color');
                setActiveColorBox(textColorBoxes, color);
                localStorage.setItem('selectedTextColor', color);
            });
        });

        // Load saved colors on page load
        const savedBgColor = localStorage.getItem('selectedBgColor');
        const savedTextColor = localStorage.getItem('selectedTextColor');

        if (savedBgColor) {
            const bgTargets = document.querySelector(`.bg-color-box[data-color="${savedBgColor}"]`)?.dataset.target.split(', ');
            if (bgTargets) {
                applyColor(bgTargets, savedBgColor, 'background-color');
                setActiveColorBox(bgColorBoxes, savedBgColor);
            }
        }

        if (savedTextColor) {
            const textTargets = document.querySelector(`.text-color-box[data-color="${savedTextColor}"]`)?.dataset.target.split(', ');
            if (textTargets) {
                applyColor(textTargets, savedTextColor, 'color');
                setActiveColorBox(textColorBoxes, savedTextColor);
            }
        }

        // Set initial active state if no color is saved (default to white background and dark text)
        if (!savedBgColor) {
            setActiveColorBox(bgColorBoxes, '#ffffff');
        }
        if (!savedTextColor) {
            setActiveColorBox(textColorBoxes, '#000000');
        }
    });
</script>
