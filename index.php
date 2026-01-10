<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>FC 23 Pro Controller</title>
    <style>
        :root { --accent: #00d2ff; --bg: #0a0a0c; }
        body { background: var(--bg); color: white; margin: 0; overflow: hidden; font-family: sans-serif; user-select: none; touch-action: none; }
        
        /* Menu System */
        #menu { position: fixed; inset: 0; background: rgba(0,0,0,0.98); z-index: 5000; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .box { background: #1c1c22; padding: 30px; border-radius: 20px; width: 320px; text-align: center; border: 1px solid #333; }
        input, select { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #444; background: #000; color: white; box-sizing: border-box; font-size: 16px; }
        .b-main { background: var(--accent); color: black; border: none; padding: 15px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer; font-size: 16px; margin-top:10px; }
        .b-edit { background: #444; color: white; margin-top: 10px; width: 100%; padding: 12px; border-radius: 8px; border: none; cursor: pointer; }

        /* UI elements */
        #controller { display: none; width: 100vw; height: 100vh; position: relative; }
        .btn { position: absolute; border: 1.5px solid #ccc; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: white; color: #111; font-weight: bold; font-size: 14px; z-index: 5; }
        .btn.active { background: var(--accent) !important; color: white !important; transform: scale(0.92); }
        .stick { position: absolute; border: 2px solid #555; border-radius: 50%; background: rgba(255,255,255,0.05); z-index: 5; }
        .knob { position: absolute; width: 40%; height: 40%; background: #0088aa; border-radius: 50%; top: 30%; left: 30%; pointer-events: none; }
        
        /* Top Center Controls */
        #top-bar { position: absolute; top: 10px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 100; }
        .util-btn { padding: 10px 20px; background: rgba(255,255,255,0.2); border: 1px solid white; color: white; border-radius: 5px; font-size: 12px; font-weight: bold; }

        /* Resize UI */
        #resizer-ui { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: #222; padding: 15px; border-radius: 50px; display: none; align-items: center; gap: 10px; z-index: 6000; border: 2px solid var(--accent); }
        input[type=range] { width: 180px; height: 30px; }
        #save-edit { position: fixed; top: 15px; right: 15px; background: #2ecc71; padding: 12px 20px; border-radius: 8px; z-index: 6000; display: none; border: none; font-weight: bold; color: white; }

        .editing .btn, .editing .stick { outline: 2px dashed orange; opacity: 0.6; }
        .selected { outline: 4px solid var(--accent) !important; opacity: 1 !important; box-shadow: 0 0 15px var(--accent); z-index: 10; }
    </style>
</head>
<body>

    <div id="menu">
        <div class="box">
            <h2 style="color:var(--accent); margin:0 0 20px 0;">FC 23 PRO</h2>
            <input type="text" id="pc-ip" placeholder="PC IP (e.g. 192.168.1.5)">
            <label style="font-size: 12px; color: #888;">SELECT PRESET TO USE:</label>
            <select id="preset-selector">
                <option value="1">Preset 1</option><option value="2">Preset 2</option>
                <option value="3">Preset 3</option><option value="4">Preset 4</option>
            </select>
            <button class="b-main" onclick="connect()">CONNECT & PLAY</button>
            <button class="b-edit" onclick="startEdit()">OPEN LAYOUT EDITOR</button>
        </div>
    </div>

    <div id="top-bar">
        <button class="util-btn" onclick="toggleFS()">⛶ FULLSCREEN</button>
        <button class="util-btn" onclick="location.reload()">DISCONNECT</button>
    </div>

    <button id="save-edit" onclick="exitEdit()">SAVE LAYOUT</button>
    <div id="resizer-ui" ontouchstart="event.stopPropagation()">
        <span style="color:var(--accent); font-size: 12px;">RESIZE:</span>
        <input type="range" id="size-slider" min="40" max="350">
    </div>

    <div id="controller">
        <!-- Shoulders / Triggers -->
        <div id="lt" class="btn" style="width:110px; height:45px; left:5%; top:5%">LT</div>
        <div id="lb" class="btn" style="width:110px; height:45px; left:22%; top:5%">LB</div>
        <div id="rb" class="btn" style="width:110px; height:45px; right:22%; top:5%">RB</div>
        <div id="rt" class="btn" style="width:110px; height:45px; right:5%; top:5%">RT</div>

        <!-- Middle Buttons -->
        <div id="back" class="btn" style="width:90px; height:40px; left:35%; top:25%">Back</div>
        <div id="start" class="btn" style="width:90px; height:40px; right:35%; top:25%">Start</div>
        <div id="ls_clk" class="btn" style="width:90px; height:40px; left:35%; top:42%">LS CLK</div>
        <div id="rs_clk" class="btn" style="width:90px; height:40px; right:35%; top:42%">RS CLK</div>

        <!-- Joysticks -->
        <div id="ls" class="stick" style="width:140px; height:140px; left:8%; bottom:15%"><div class="knob"></div></div>
        <div id="rs" class="stick" style="width:140px; height:140px; left:52%; bottom:15%"><div class="knob"></div></div>

        <!-- D-Pad -->
        <div id="du" class="btn" style="width:45px; height:80px; left:38%; bottom:30%"></div>
        <div id="dd" class="btn" style="width:45px; height:80px; left:38%; bottom:5%"></div>
        <div id="dl" class="btn" style="width:80px; height:45px; left:31%; bottom:18%"></div>
        <div id="dr" class="btn" style="width:80px; height:45px; left:42%; bottom:18%"></div>

        <!-- Face Buttons -->
        <div id="y" class="btn" style="width:85px; height:85px; right:15%; bottom:48%; border-radius:50%">Y</div>
        <div id="x" class="btn" style="width:85px; height:85px; right:25%; bottom:30%; border-radius:50%">X</div>
        <div id="b" class="btn" style="width:85px; height:85px; right:5%; bottom:30%; border-radius:50%">B</div>
        <div id="a" class="btn" style="width:85px; height:85px; right:15%; bottom:12%; border-radius:50%">A</div>
    </div>

    <script>
        let ws, isEdit = false, activeSlot = '1', selectedEl = null;
        const state = { ls:[0,0], rs:[0,0], btns:{} }, touchMap = new Map();

        // 1. Slider Setup
        document.getElementById('size-slider').addEventListener('input', (e) => {
            if (selectedEl) {
                let v = e.target.value + 'px';
                selectedEl.style.width = v;
                if (selectedEl.classList.contains('stick') || selectedEl.id.length === 1) selectedEl.style.height = v;
            }
        });

        // 2. Preset Logic
        function saveLayout() {
            const cfg = {};
            document.querySelectorAll('.btn, .stick').forEach(el => {
                cfg[el.id] = { l: el.style.left, t: el.style.top, r: el.style.right, b: el.style.bottom, w: el.style.width, h: el.style.height };
            });
            localStorage.setItem('fc_lay_' + activeSlot, JSON.stringify(cfg));
        }

        function loadLayout(slot) {
            const cfg = JSON.parse(localStorage.getItem('fc_lay_' + slot));
            if (!cfg) return;
            Object.keys(cfg).forEach(id => {
                const el = document.getElementById(id); if (!el) return;
                const c = cfg[id];
                el.style.left = c.l; el.style.top = c.t; el.style.right = c.r; el.style.bottom = c.b;
                el.style.width = c.w; el.style.height = c.h;
            });
        }

        // 3. System Actions
        function toggleFS() {
            if (!document.fullscreenElement) document.documentElement.requestFullscreen().catch(e => alert("Use 'Add to Home Screen' for Fullscreen on iOS"));
            else document.exitFullscreen();
        }

        function startEdit() {
            activeSlot = prompt("Edit Preset # (1-4):", "1") || "1";
            isEdit = true;
            document.getElementById('menu').style.display = 'none';
            document.getElementById('controller').style.display = 'block';
            document.getElementById('save-edit').style.display = 'block';
            document.body.classList.add('editing');
            loadLayout(activeSlot);
        }

        function exitEdit() { saveLayout(); location.reload(); }

        // 4. Touch Handling
        document.addEventListener('touchstart', e => {
            for (let t of e.changedTouches) {
                const el = t.target.closest('.btn, .stick');
                if (isEdit) {
                    if (el) {
                        if (selectedEl) selectedEl.classList.remove('selected');
                        selectedEl = el; el.classList.add('selected');
                        document.getElementById('resizer-ui').style.display = 'flex';
                        document.getElementById('size-slider').value = parseInt(el.style.width);
                    } else if (t.target.id === 'controller') {
                        if (selectedEl) selectedEl.classList.remove('selected');
                        selectedEl = null; document.getElementById('resizer-ui').style.display = 'none';
                    }
                } else if (el) {
                    el.classList.add('active');
                    if (!el.id.includes('ls') && !el.id.includes('rs')) state.btns[el.id] = 1;
                }
                if (el) touchMap.set(t.identifier, { id: el.id });
            }
        });

        document.addEventListener('touchmove', e => {
            e.preventDefault();
            for (let t of e.changedTouches) {
                const data = touchMap.get(t.identifier); if (!data) continue;
                const el = document.getElementById(data.id);
                if (isEdit) {
                    el.style.left = (t.clientX - el.offsetWidth/2) + 'px';
                    el.style.top = (t.clientY - el.offsetHeight/2) + 'px';
                    el.style.bottom = 'auto'; el.style.right = 'auto';
                } else if (data.id === 'ls' || data.id === 'rs') {
                    const r = el.getBoundingClientRect(), k = el.querySelector('.knob');
                    let x = (t.clientX - (r.left + r.width/2)) / (r.width/2), y = (t.clientY - (r.top + r.height/2)) / (r.height/2);
                    const m = Math.sqrt(x*x + y*y); if (m > 1) { x /= m; y /= m; }
                    k.style.transform = `translate(${x*40}px, ${y*40}px)`; state[data.id] = [x, y];
                }
            }
        }, {passive: false});

        document.addEventListener('touchend', e => {
            for (let t of e.changedTouches) {
                const data = touchMap.get(t.identifier); if (!data) continue;
                const el = document.getElementById(data.id);
                if (!isEdit) {
                    el.classList.remove('active');
                    if (data.id.includes('ls') || data.id.includes('rs')) {
                        el.querySelector('.knob').style.transform = ''; state[data.id] = [0, 0];
                    } else state.btns[data.id] = 0;
                }
                touchMap.delete(t.identifier);
            }
        });

        function connect() {
            const ip = document.getElementById('pc-ip').value;
            const slot = document.getElementById('preset-selector').value;
            if (!ip) return alert("Enter PC IP Address");
            ws = new WebSocket(`ws://${ip}:8765`);
            ws.onopen = () => {
                document.getElementById('menu').style.display = 'none';
                document.getElementById('controller').style.display = 'block';
                loadLayout(slot);
                setInterval(() => { if(ws.readyState===1) ws.send(JSON.stringify(state)); }, 16);
            };
            ws.onerror = () => alert("Check Server.py and IP address.");
        }
    </script>
</body>

</html>
