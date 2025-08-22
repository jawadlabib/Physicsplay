<?php
session_start();
$_SESSION['user_name'] = 'Test User';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Physics Dashboard</title>
   <style> body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #6dd5ed, #2193b0);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: rgba(0,0,0,0.6);
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 400px;
    transition: 0.3s;
}

.container:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 35px rgba(0,0,0,0.6);
}

h2 { text-align:center; margin-bottom:20px; }
label { display:block; margin-top:10px; }
input, select, button {
    width:100%;
    padding:8px;
    margin-top:5px;
    border-radius:5px;
    border:none;
}
button {
    background:#f39c12;
    color:#fff;
    cursor:pointer;
    font-weight:bold;
    transition: 0.3s;
}
button:hover { background:#e67e22; transform: scale(1.05); }</style>

    <script>
        const theoryInputs = {
            projectile: ['Initial speed (m/s)','Angle (deg)'],
            freefall: ['Height (m)'],
            coulomb: ['Charge 1 (C)','Charge 2 (C)','Distance (m)']
        };

        function updateInputs() {
            const theory = document.querySelector('select[name="theory_type"]').value;
            const container = document.getElementById('inputsContainer');
            container.innerHTML = '';
            if(!theory) return;

            theoryInputs[theory].forEach((labelText,index)=>{
                const label = document.createElement('label');
                label.textContent = labelText + ':';
                const input = document.createElement('input');
                input.type = 'number';
                input.step = 'any';
                input.name = 'param'+(index+1);
                input.required = true;
                container.appendChild(label);
                container.appendChild(input);
            });
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Welcome, <?=htmlspecialchars($_SESSION['user_name'])?></h2>
    <form method="post" action="calculate.php">
        <label>Select Theory:</label>
        <select name="theory_type" onchange="updateInputs()" required>
            <option value="">--Choose--</option>
            <option value="projectile">Projectile Motion</option>
            <option value="freefall">Free Fall Simulator</option>
            <option value="coulomb">Electric Field / Coulomb Force</option>
        </select>

        <div id="inputsContainer">
            <!-- Inputs will be generated dynamically when theory is selected -->
        </div>

        <label>Select Planet:</label>
        <select name="planet" required>
            <option value="earth">Earth (g=9.81 m/s²)</option>
            <option value="moon">Moon (g=1.62 m/s²)</option>
            <option value="mars">Mars (g=3.71 m/s²)</option>
        </select>

        <button type="submit">Calculate & Simulate</button>
    </form>
</div>
</body>
</html>
