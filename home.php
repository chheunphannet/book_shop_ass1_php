<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soft Mesh Background</title>
    <style>
        /* 1. RESET & BASE STYLES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* This is the base color of the page (light lavender/grey) */
            background-color: #E4E7F5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* Prevents scrollbars if blobs go off-screen */
            font-family: sans-serif;
        }

        /* 2. THE BACKGROUND BLOBS (The colorful glowing spots) */
        .background-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px); /* This creates the soft, diffused look */
            z-index: -1; /* Ensures they stay behind content */
            opacity: 0.8;
        }

        /* Top-Left Peach Blob */
        .blob-1 {
            width: 500px;
            height: 500px;
            background: #FFD4B8; /* Peach/Orange color */
            top: -100px;
            left: -150px;
        }

        /* Top-Right Peach Blob */
        .blob-2 {
            width: 400px;
            height: 400px;
            background: #FFD4B8; /* Peach/Orange color */
            top: -50px;
            right: -100px;
        }

        /* Bottom-Right Purple/Blue Blob */
        .blob-3 {
            width: 600px;
            height: 600px;
            background: #B6BCF5; /* Periwinkle/Blue color */
            bottom: -150px;
            right: -150px;
        }

        /* Bottom-Left Soft Pink Blob (Subtle) */
        .blob-4 {
            width: 300px;
            height: 300px;
            background: #E8C8E6; /* Soft Pink */
            bottom: 50px;
            left: -50px;
        }

        /* 3. OPTIONAL: THE GLASS CARD (To complete the look) */
        .glass-container {
            width: 80%;
            height: 80vh;
            
            /* The Glassmorphism Effect */
            background: rgba(255, 255, 255, 0.25); /* Low opacity white */
            backdrop-filter: blur(20px); /* The "Frosted" blur effect */
            -webkit-backdrop-filter: blur(20px); /* Safari support */
            
            border: 1px solid rgba(255, 255, 255, 0.5); /* Subtle white border */
            border-radius: 30px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1); /* Soft shadow */
            
            /* Text styling just for demo */
            display: flex;
            justify-content: center;
            align-items: center;
            color: #4A4A6A;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="background-blob blob-1"></div>
    <div class="background-blob blob-2"></div>
    <div class="background-blob blob-3"></div>
    <div class="background-blob blob-4"></div>

    <div class="glass-container">
        Content goes here
    </div>

</body>
</html>