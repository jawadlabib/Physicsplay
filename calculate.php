<?php
session_start();
$theory = $_POST['theory_type'] ?? '';
$planet = $_POST['planet'] ?? 'earth';
$p1 = floatval($_POST['param1'] ?? 0);
$p2 = floatval($_POST['param2'] ?? 0);
$p3 = floatval($_POST['param3'] ?? 0);

// Set gravity based on planet
$g = 9.81;
if($planet=='moon') $g=1.62;
if($planet=='mars') $g=3.71;

$result = [];
$animationData = [];

switch($theory){
    case 'projectile':
        $v=$p1; 
        $theta=deg2rad($p2);
        $result['Range']=($v*$v*sin(2*$theta))/$g;
        $result['Max Height']=($v*$v*pow(sin($theta),2))/(2*$g);
        $result['Time of Flight']=(2*$v*sin($theta))/$g;
        $animationData = ['v'=>$v,'theta'=>$theta,'g'=>$g];
        break;
    case 'freefall':
        $h=$p1;
        $result['Time']=sqrt(2*$h/$g);
        $result['Final Velocity']=sqrt(2*$g*$h);
        $animationData = ['h'=>$h,'g'=>$g];
        break;
    case 'coulomb':
        $q1=$p1; $q2=$p2; $r=$p3; $k=8.9875517923e9;
        $result['Force']=$k*($q1*$q2)/($r*$r);
        $animationData = ['q1'=>$q1,'q2'=>$q2,'r'=>$r,'k'=>$k];
        break;
    default:
        $result['Error']='Invalid theory type';
        break;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Simulation Result</title>
<style>
body{font-family:Arial,sans-serif;margin:20px;background:#eef}
h2{text-align:center;color:#333}
.results,canvas{margin:0 auto;border-radius:10px;width:90%;max-width:500px}
.results{padding:20px;background:#fff}
canvas{display:block;margin:20px auto;background:#ddf;border:1px solid #000;border-radius:5px;height:auto}
ul{margin-top:10px}
a{display:block;text-align:center;margin-top:20px;text-decoration:none;color:blue}
</style>
</head>
<body>
<h2><?=htmlspecialchars($theory)?> Simulation on <?=ucfirst($planet)?></h2>
<div class="results">
<ul>
<?php foreach($result as $k=>$v): ?>
<li><?=htmlspecialchars($k)?> : <?=htmlspecialchars(number_format($v,4))?></li>
<?php endforeach; ?>
</ul>
</div>

<canvas id="simCanvas" width="500" height="300"></canvas>
<a href="dashboard.php">Back to Dashboard</a>

<script>
const theory = "<?=$theory?>";
const data = <?=json_encode($animationData)?>;
const canvas = document.getElementById('simCanvas');
const ctx = canvas.getContext('2d');

function clear() { ctx.clearRect(0,0,canvas.width,canvas.height); }

if(theory==='projectile'){
    const v = data.v;
    const theta = data.theta;
    const g = data.g;

    // Total flight time
    const totalTime = (2 * v * Math.sin(theta)) / g;

    // Range and max height
    const range = (v * v * Math.sin(2*theta)) / g;
    const maxH = (v * v * Math.pow(Math.sin(theta),2)) / (2*g);

    // Scaling factors
    const xScale = canvas.width / (range * 1.1);
    const yScale = canvas.height / (maxH * 1.2);

    // Precompute points
    const points = [];
    const dt = 0.02;
    for(let t=0; t<=totalTime; t+=dt){
        const x = v * Math.cos(theta) * t;
        const y = v * Math.sin(theta) * t - 0.5 * g * t * t;
        points.push({
            x: x * xScale,
            y: canvas.height - y * yScale
        });
    }

    // Animate projectile
    let index = 0;
    function draw(){
        clear();
        // Draw projectile
        const pt = points[index];
        ctx.beginPath();
        ctx.arc(pt.x, pt.y, 8, 0, 2*Math.PI);
        ctx.fillStyle='red';
        ctx.fill();

        // Draw trajectory so far
        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        for(let i=1;i<=index;i++){
            ctx.lineTo(points[i].x, points[i].y);
        }
        ctx.strokeStyle='rgba(255,0,0,0.5)';
        ctx.stroke();

        index++;
        if(index < points.length) requestAnimationFrame(draw);
    }
    draw();

}else if(theory==='freefall'){
    let t=0;
    const dt=0.05;
    const h=data.h, g=data.g;
    function draw(){
        clear();
        let y = h - 0.5*g*t*t;
        const drawY = canvas.height - y;
        ctx.beginPath();
        ctx.arc(canvas.width/2, drawY, 10, 0, 2*Math.PI);
        ctx.fillStyle='green';
        ctx.fill();
        t+=dt;
        if(drawY<canvas.height) requestAnimationFrame(draw);
    }
    draw();
}else if(theory==='coulomb'){
    const q1 = data.q1, q2 = data.q2, r = data.r, k = data.k;
    const centerX = canvas.width/2;
    const centerY = canvas.height/2;
    const dist = Math.min(r*50, 200);
    function draw(){
        clear();
        ctx.beginPath();
        ctx.arc(centerX - dist/2, centerY, 15,0,2*Math.PI);
        ctx.fillStyle='blue';
        ctx.fill();
        ctx.beginPath();
        ctx.arc(centerX + dist/2, centerY, 15,0,2*Math.PI);
        ctx.fillStyle='red';
        ctx.fill();
        ctx.beginPath();
        ctx.moveTo(centerX - dist/2, centerY);
        ctx.lineTo(centerX + dist/2, centerY);
        ctx.strokeStyle='black';
        ctx.stroke();
    }
    draw();
}
</script>
</body>
</html>
