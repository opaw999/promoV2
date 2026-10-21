<style>
    .cont {
        display: flex;
        align-items: center;
        justify-content: center;

    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 d-flex">
                <div class="card radius-10 w-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Number of Active Promo per Store</h5>
                        <hr class="mt-2">
                        <div class="row">
                            <div class="col-lg-7 col-xl-7 cont">
                                <div class="chart-container-1" style="z-index: 1;">
                                    <canvas id="chart4"></canvas>
                                </div>
                            </div>
                            <div class="col-lg-5 col-xl-5">
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $i = 0;
                                    $bus = $this->promo_model->locate_promo_bu($this->hrCode);
                                    foreach ($bus as $value) {

                                        $count = $this->promo_model->active_promoStore($value['bunit_field']);
                                        echo    '<li class="list-group-item d-flex bg-transparent align-items-center" style="font-size: 11px;">
                                                    <i class="bx bxs-circle me-1" style="color: ' . $color[$i] . '"></i>
                                                    ' . $value['bunit_name'] . ' 
                                                    <span class="ms-auto"><b>' . $count['num'] . '</b></span>
                                                </li>';
                                        $i++;
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 mt-3">
                            <!-- <div class="col">
                                <div class="card radius-10 overflow-hidden mb-0 shadow-none border">
                                    <div class="card-body">
                                        <h6 class="card-title cursor-pointer text-center mb-0">
                                            <a href="<?= base_url('promo/page/reports/statisticRep') ?>">
                                                Statistics Report
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="col">
                                <div class="card radius-10 overflow-hidden mb-0 shadow-none border">
                                    <div class="card-body">
                                        <h6 class="card-title cursor-pointer mb-0">
                                            <a href="<?= (count($failedEpas) == 0) ? 'javascript:;' : base_url('guest/page/reports/failedEpas') ?>">
                                                Failed EPAS <span class="float-end badge bg-danger rounded-pill"><?= count($failedEpas) ?></span>
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="col">
                                <div class="card radius-10 overflow-hidden mb-0 shadow-none border">
                                    <div class="card-body">
                                        <h6 class="card-title cursor-pointer mb-0">
                                            <a href="<?= (count($bday) == 0) ? 'javascript:;' : base_url('guest/page/reports/bdayToday') ?>">
                                                Birthdays Today <span class="float-end badge bg-danger rounded-pill"><?= count($bday) ?></span>
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5 col-xl-4 d-flex">
                <div class="card w-100 radius-10">
                    <div class="card-body">
                        <a href="<?= (count($newPromo) == 0) ? 'javascript:;' : base_url('guest/dashboard/newPromo') ?>">
                            <div class="card radius-10 border shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>

                                            <p class="mb-0 text-secondary">New Promo</p>
                                            <h4 class="my-1"><?= count($newPromo) ?></h4>
                                            <p class="mb-0 font-13">tagged from last month. </p>
                                        </div>
                                        <div class="widgets-icons-2 bg-gradient-blues text-white ms-auto" style="z-index: 1;"><i class='bx bx-user-plus'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <a href="<?= (count($total) == 0) ? 'javascript:;' : base_url('guest/promo/masterfile') ?>">
                            <div class="card radius-10 border shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">Active Promo </p>
                                            <h4 class="my-1"><?= count($total) ?></h4>
                                            <p class="mb-0 font-13">as of <?= date("F j, Y") ?>. </p>
                                        </div>
                                        <div class="widgets-icons-2 bg-gradient-ohhappiness text-white ms-auto" style="z-index: 1;"><i class='bx bxs-group'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <!-- <a href="<?= (count($eoc) == 0) ? 'javascript:;' : base_url('guest/contract/eoclist/today'); ?>">
                            <div class="card radius-10 border shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 text-secondary">End of Contract</p>
                                            <h4 class="my-1"><?= count($eoc) ?></h4>
                                            <p class="mb-0 font-13">today <?= date("F j, Y") ?>. </p>
                                        </div>
                                        <div class="widgets-icons-2 bg-gradient-kyoto text-white ms-auto" style="z-index: 1;"><i class='bx bx-calendar-exclamation'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a> -->
                    </div>
                </div>
            </div>
        </div><!--end row-->
    </div>
</div>

<?php if (date('m') >= '9') { ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let canvas = document.createElement("canvas");
            canvas.id = "snow";
            canvas.style.position = "fixed";
            canvas.style.top = "0";
            canvas.style.left = "0";
            canvas.style.pointerEvents = "none";
            canvas.style.zIndex = "0";
            document.body.appendChild(canvas);

            let ctx = canvas.getContext("2d");
            let snowflakes = [];

            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            window.addEventListener("resize", resize);
            resize();

            // ---- Shape Draw Functions ----
            function drawStar(ctx, x, y, radius, color) {
                ctx.save();
                ctx.fillStyle = color;
                ctx.beginPath();
                ctx.translate(x, y);
                ctx.moveTo(0, 0 - radius);
                for (let i = 0; i < 5; i++) {
                    ctx.rotate(Math.PI / 5);
                    ctx.lineTo(0, 0 - (radius * 0.5));
                    ctx.rotate(Math.PI / 5);
                    ctx.lineTo(0, 0 - radius);
                }
                ctx.fill();
                ctx.restore();
            }

            function drawHexagon(ctx, x, y, radius, color) {
                ctx.save();
                ctx.strokeStyle = color;
                ctx.beginPath();
                ctx.translate(x, y);
                for (let i = 0; i < 6; i++) {
                    ctx.lineTo(
                        radius * Math.cos((Math.PI / 3) * i),
                        radius * Math.sin((Math.PI / 3) * i)
                    );
                }
                ctx.closePath();
                ctx.stroke();
                ctx.restore();
            }

            function drawCross(ctx, x, y, radius, color) {
                ctx.strokeStyle = color;
                ctx.beginPath();
                ctx.moveTo(x - radius, y);
                ctx.lineTo(x + radius, y);
                ctx.moveTo(x, y - radius);
                ctx.lineTo(x, y + radius);
                ctx.stroke();
            }

            // ---- Snow Update Loop ----
            function updateSnow() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                snowflakes.forEach((flake) => {
                    flake.y += flake.speedY;
                    flake.x += flake.speedX;
                    if (flake.y > canvas.height) {
                        flake.y = -5;
                        flake.x = Math.random() * canvas.width;
                    }

                    // twinkle effect for ✦
                    if (flake.type === "star2") {
                        flake.alpha = 0.5 + 0.5 * Math.sin(Date.now() / 40 + flake.phase);
                    } else {
                        flake.alpha = 1;
                    }

                    ctx.globalAlpha = flake.alpha;
                    ctx.fillStyle = flake.color;

                    if (flake.type === "circle") {
                        ctx.beginPath();
                        ctx.arc(flake.x, flake.y, flake.radius, 0, Math.PI * 2);
                        ctx.fill();
                    } else if (flake.type === "snowflake") {
                        ctx.font = flake.radius * 4 + "px Arial";
                        ctx.fillText("❄", flake.x, flake.y);
                    } else if (flake.type === "snowflake1") {
                        ctx.font = flake.radius * 4 + "px Arial";
                        ctx.fillText("❄", flake.x, flake.y);
                    } else if (flake.type === "star") {
                        drawStar(ctx, flake.x, flake.y, flake.radius * 2, flake.color);
                    } else if (flake.type === "hexagon") {
                        drawHexagon(ctx, flake.x, flake.y, flake.radius * 2, flake.color);
                    } else if (flake.type === "cross") {
                        drawCross(ctx, flake.x, flake.y, flake.radius, flake.color);
                    } else if (flake.type === "star1") {
                        ctx.font = (flake.radius * 3) + "px 'Segoe UI Symbol','Arial Unicode MS',sans-serif";
                        ctx.fillText("✦", flake.x, flake.y);
                    } else if (flake.type === "star2") {
                        ctx.font = (flake.radius * 3) + "px 'Segoe UI Symbol','Arial Unicode MS',sans-serif";
                        ctx.fillText("✦", flake.x, flake.y);
                    }

                    ctx.globalAlpha = 1; // reset for next draw
                });
                requestAnimationFrame(updateSnow);
            }

            // ---- Create Snowflakes ----
            function createSnowflakes() {
                const numFlakes = 50;
                const colors = ["white", "silver", "#7fe6fac7", "#87ceeb", "#d9d9d9"];
                const types = ["circle", "snowflake", "snowflake1", "star1", "star2"];
                for (let i = 0; i < numFlakes; i++) {
                    let type = types[Math.floor(Math.random() * types.length)];
                    snowflakes.push({
                        x: Math.random() * canvas.width,
                        y: -Math.random() * canvas.height,
                        radius: Math.random() * 3 + 2,
                        speedY: Math.random() + 1,
                        speedX: (Math.random() - 0.5) * 0.7,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        type: type,
                        phase: Math.random() * Math.PI * 2 // twinkle timing offset
                    });
                }
            }

            createSnowflakes();
            updateSnow();
        });
    </script>


<?php } ?>
<!--end page wrapper -->