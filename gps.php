<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS-Tagged Survey</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, textarea { width: 100%; padding: 8px; }
        button { padding: 10px 20px; background-color: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        #status { margin-top: 10px; color: green; }
        #error { margin-top: 10px; color: red; }
    </style>
</head>
<body>
    <h1>GPS-Tagged Survey Submission</h1>
    <form id="surveyForm">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" required></textarea>
        </div>
        <div class="form-group">
            <label for="latitude">Latitude:</label>
            <input type="text" id="latitude" name="latitude" readonly>
        </div>
        <div class="form-group">
            <label for="longitude">Longitude:</label>
            <input type="text" id="longitude" name="longitude" readonly>
        </div>
        <button type="button" onclick="getLocation()">Get GPS Location</button>
        <button type="submit">Submit Survey</button>
    </form>
    <div id="status"></div>
    <div id="error"></div>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById("latitude").value = position.coords.latitude;
                        document.getElementById("longitude").value = position.coords.longitude;
                        document.getElementById("status").innerText = "GPS location captured successfully!";
                        document.getElementById("error").innerText = "";
                    },
                    (error) => {
                        document.getElementById("error").innerText = "Error capturing GPS: " + error.message;
                        document.getElementById("status").innerText = "";
                    }
                );
            } else {
                document.getElementById("error").innerText = "Geolocation is not supported by this browser.";
            }
        }

        document.getElementById("surveyForm").addEventListener("submit", async (e) => {
            e.preventDefault();
            const name = document.getElementById("name").value;
            const comment = document.getElementById("comment").value;
            const latitude = document.getElementById("latitude").value;
            const longitude = document.getElementById("longitude").value;

            if (!latitude || !longitude) {
                document.getElementById("error").innerText = "Please capture GPS location before submitting.";
                return;
            }

            const surveyData = {
                name,
                comment,
                latitude: parseFloat(latitude),
                longitude: parseFloat(longitude),
                timestamp: new Date().toISOString()
            };

            try {
                // Replace with your backend API endpoint
                const response = await fetch('https://your-backend-api.com/submit-survey', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(surveyData)
                });

                if (response.ok) {
                    document.getElementById("status").innerText = "Survey submitted successfully!";
                    document.getElementById("surveyForm").reset();
                } else {
                    document.getElementById("error").innerText = "Failed to submit survey.";
                }
            } catch (error) {
                document.getElementById("error").innerText = "Error: " + error.message;
            }
        });
    </script>
</body>
</html>