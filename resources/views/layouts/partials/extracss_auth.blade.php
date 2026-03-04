   <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /* ================================
           BACKGROUND OPTIONS
           ================================ */
         /* Note: Only uncomment one of the options below. */
        /* OPTION 1: Background Color/Gradient */
        /* Uncomment below for color background */
        
        /* html {
            height: 100%;
            background: linear-gradient(to right, #6366f1, #3b82f6);
        } */
       
        
        /* OPTION 2: Background Image */
        /* Uncomment below for image background */
        html {
            height: 100%;
            background-image: url('{{ asset('images/diff_eyewear_cover.jfif') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        html::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        
        /* Common Body Styles (Always Keep This) */
        body {
            min-height: 100vh;
            background: transparent;
            margin: 0;
            padding: 0;
            position: relative;
            z-index: 1;
        }

        h1 {
            color: #fff;
        }
    </style>

    <style type="text/css">
        /*
      * Pattern lock css
      * Pattern direction
      * http://ignitersworld.com/lab/patternLock.html
      */
        .patt-wrap {
            z-index: 10;
        }

        .patt-circ.hovered {
            background-color: #cde2f2;
            border: none;
        }

        .patt-circ.hovered .patt-dots {
            display: none;
        }

        .patt-circ.dir {
            background-image: url("http://pos.test/img/pattern-directionicon-arrow.png");
            background-position: center;
            background-repeat: no-repeat;
        }

        .patt-circ.e {
            -webkit-transform: rotate(0);
            transform: rotate(0);
        }

        .patt-circ.s-e {
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .patt-circ.s {
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
        }

        .patt-circ.s-w {
            -webkit-transform: rotate(135deg);
            transform: rotate(135deg);
        }

        .patt-circ.w {
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .patt-circ.n-w {
            -webkit-transform: rotate(225deg);
            transform: rotate(225deg);
        }

        .patt-circ.n {
            -webkit-transform: rotate(270deg);
            transform: rotate(270deg);
        }

        .patt-circ.n-e {
            -webkit-transform: rotate(315deg);
            transform: rotate(315deg);
        }
    </style>
    <style>
        h1 {
            color: #fff;
        }
    </style>
    <style>
        .action-link[data-v-1552a5b6] {
            cursor: pointer;
        }
    </style>
    <style>
        .action-link[data-v-397d14ca] {
            cursor: pointer;
        }
    </style>
    <style>
        .action-link[data-v-49962cc0] {
            cursor: pointer;
        }
        
        /* Custom button colors with #48b2ee */
        .tw-dw-btn-primary,
        a.tw-dw-btn-primary {
            background-color: #48b2ee !important;
            border-color: #48b2ee !important;
            color: white !important;
        }
        
        .tw-dw-btn-primary:hover,
        a.tw-dw-btn-primary:hover {
            background-color: #3a9dd9 !important;
            border-color: #3a9dd9 !important;
            opacity: 0.9;
        }
        
        .tw-dw-btn-primary:active,
        a.tw-dw-btn-primary:active {
            background-color: #2d8bc4 !important;
            border-color: #2d8bc4 !important;
        }
        
        /* Wizard action buttons */
        .actions a[href="#next"],
        .actions a[href="#finish"] {
            background-color: #48b2ee !important;
            border-color: #48b2ee !important;
            color: white !important;
        }
        
        .actions a[href="#next"]:hover,
        .actions a[href="#finish"]:hover {
            background-color: #3a9dd9 !important;
            opacity: 0.9;
        }
    </style>

<link href="{{ asset('css/tailwind/app.css') }}" rel="stylesheet">
