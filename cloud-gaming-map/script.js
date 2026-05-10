const config = {
    nodes: [
        { id: 'us-west', name: 'US West (San Jose)', coords: [-121.8863, 37.3382], latency: '12ms', status: 'Optimal', load: 35, datacenters: 12 },
        { id: 'us-east', name: 'US East (Ashburn)', coords: [-77.4875, 39.0438], latency: '18ms', status: 'Optimal', load: 52, datacenters: 18 },
        { id: 'us-central', name: 'US Central (Dallas)', coords: [-96.7970, 32.7767], latency: '25ms', status: 'Optimal', load: 48, datacenters: 10 },
        { id: 'eu-west', name: 'Europe West (London)', coords: [-0.1278, 51.5074], latency: '15ms', status: 'Optimal', load: 41, datacenters: 14 },
        { id: 'eu-central', name: 'Europe Central (Frankfurt)', coords: [8.6821, 50.1109], latency: '22ms', status: 'Optimal', load: 68, datacenters: 22 },
        { id: 'eu-north', name: 'Europe North (Stockholm)', coords: [18.0686, 59.3293], latency: '28ms', status: 'Optimal', load: 32, datacenters: 8 },
        { id: 'asia-east', name: 'Asia East (Tokyo)', coords: [139.6917, 35.6895], latency: '28ms', status: 'Stable', load: 74, datacenters: 10 },
        { id: 'asia-se', name: 'Asia SE (Singapore)', coords: [103.8198, 1.3521], latency: '35ms', status: 'Stable', load: 45, datacenters: 8 },
        { id: 'asia-south', name: 'Asia South (Mumbai)', coords: [72.8777, 19.0760], latency: '38ms', status: 'Stable', load: 55, datacenters: 7 },
        { id: 'me-west', name: 'Middle East (Dubai)', coords: [55.2708, 25.2048], latency: '40ms', status: 'Stable', load: 42, datacenters: 6 },
        { id: 'sa-east', name: 'South America (Sao Paulo)', coords: [-46.6333, -23.5505], latency: '42ms', status: 'Stable', load: 28, datacenters: 6 },
        { id: 'au-east', name: 'Australia East (Sydney)', coords: [151.2093, -33.8688], latency: '48ms', status: 'Stable', load: 31, datacenters: 5 },
        { id: 'af-south', name: 'Africa South (Cape Town)', coords: [18.4241, -33.9249], latency: '55ms', status: 'Stable', load: 22, datacenters: 4 }
    ],
    connections: [
        ['us-west', 'us-central'],
        ['us-central', 'us-east'],
        ['us-east', 'eu-west'],
        ['eu-west', 'eu-central'],
        ['eu-central', 'eu-north'],
        ['eu-central', 'me-west'],
        ['me-west', 'asia-south'],
        ['asia-south', 'asia-se'],
        ['asia-se', 'asia-east'],
        ['asia-east', 'us-west'],
        ['sa-east', 'us-east'],
        ['au-east', 'asia-se'],
        ['af-south', 'eu-west']
    ]
};

const container = document.getElementById('map-canvas');
const width = container.clientWidth;
const height = container.clientHeight;

const svg = d3.select("#map-canvas")
    .append("svg")
    .attr("width", "100%")
    .attr("height", "100%")
    .attr("viewBox", `0 0 ${width} ${height}`)
    .attr("preserveAspectRatio", "xMidYMid slice");

const g = svg.append("g");

const projection = d3.geoMercator()
    .scale(width / 6.5)
    .translate([width / 2, height / 1.5]);

const path = d3.geoPath().projection(projection);

const zoom = d3.zoom()
    .scaleExtent([1, 8])
    .on("zoom", (event) => {
        g.attr("transform", event.transform);
    });

svg.call(zoom);

// Load World Data
d3.json("https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json").then(world => {
    const countries = topojson.feature(world, world.objects.countries);

    // Draw grid
    const graticule = d3.geoGraticule();
    g.append("path")
        .datum(graticule)
        .attr("class", "graticule")
        .attr("d", path)
        .style("fill", "none")
        .style("stroke", "#eee")
        .style("stroke-width", "0.5px");

    g.selectAll(".country")
        .data(countries.features)
        .enter()
        .append("path")
        .attr("class", "country")
        .attr("d", path)
        .style("fill", d => {
            // Randomly color some countries to simulate "demand heatmap"
            const demand = Math.random();
            if (demand > 0.8) return "#f0f7ff";
            if (demand > 0.6) return "#fdfdfd";
            return "#f5f5f5";
        });

    drawConnections();
    drawNodes();
});

function drawConnections() {
    const lineGroup = g.append("g").attr("class", "connections");

    config.connections.forEach(conn => {
        const source = config.nodes.find(n => n.id === conn[0]);
        const target = config.nodes.find(n => n.id === conn[1]);

        if (source && target) {
            const sourceCoords = projection(source.coords);
            const targetCoords = projection(target.coords);

            // Create curved lines
            const dx = targetCoords[0] - sourceCoords[0];
            const dy = targetCoords[1] - sourceCoords[1];
            const dr = Math.sqrt(dx * dx + dy * dy) * 1.5;

            const d = `M${sourceCoords[0]},${sourceCoords[1]}A${dr},${dr} 0 0,1 ${targetCoords[0]},${targetCoords[1]}`;

            const pathLine = lineGroup.append("path")
                .attr("d", d)
                .attr("class", "connection-line")
                .style("fill", "none")
                .style("stroke", "var(--accent-color)")
                .style("stroke-width", "0.5px")
                .style("opacity", "0.2")
                .style("stroke-dasharray", "4,4");

            // Add particle
            animateParticle(lineGroup, d);
        }
    });
}

function animateParticle(parent, pathData) {
    const particle = parent.append("circle")
        .attr("r", 1.5)
        .attr("fill", "var(--accent-color)")
        .attr("opacity", 0.8);

    const tempPath = document.createElementNS("http://www.w3.org/2000/svg", "path");
    tempPath.setAttribute("d", pathData);
    const length = tempPath.getTotalLength();

    function repeat() {
        particle
            .transition()
            .duration(3000 + Math.random() * 3000)
            .ease(d3.easeLinear)
            .attrTween("transform", function() {
                return function(t) {
                    const point = tempPath.getPointAtLength(t * length);
                    return `translate(${point.x},${point.y})`;
                };
            })
            .on("end", repeat);
    }
    repeat();
}

function drawNodes() {
    const nodeGroup = g.append("g").attr("class", "nodes");

    const nodes = nodeGroup.selectAll(".node-container")
        .data(config.nodes)
        .enter()
        .append("g")
        .attr("class", "node-container")
        .attr("transform", d => `translate(${projection(d.coords)})`)
        .on("click", (event, d) => showDetails(d));

    // Outer pulse
    nodes.append("circle")
        .attr("class", "node-pulse")
        .attr("r", 4)
        .style("fill", d => getNodeColor(d.status))
        .each(function() {
            pulse(d3.select(this));
        });

    // Inner dot
    nodes.append("circle")
        .attr("class", "node-dot")
        .attr("r", 3)
        .style("fill", d => getNodeColor(d.status))
        .style("stroke", "#fff")
        .style("stroke-width", "1px");

    // Labels (optional, keep it clean)
    nodes.append("text")
        .attr("dy", -10)
        .attr("text-anchor", "middle")
        .style("font-size", "8px")
        .style("fill", "var(--text-muted)")
        .style("pointer-events", "none")
        .text(d => d.name.split(' (')[0]);
}

function pulse(circle) {
    circle.transition()
        .duration(2000)
        .attr("r", 12)
        .style("opacity", 0)
        .on("end", function() {
            d3.select(this).attr("r", 4).style("opacity", 0.4);
            pulse(d3.select(this));
        });
}

function getNodeColor(status) {
    switch(status) {
        case 'Optimal': return 'var(--node-optimal)';
        case 'Stable': return 'var(--node-stable)';
        default: return 'var(--node-high)';
    }
}

function showDetails(node) {
    const panel = d3.select("#overlay-panel");
    panel.classed("hidden", false);

    d3.select("#region-name").text(node.name);
    d3.select("#stat-latency").text(node.latency);
    d3.select("#stat-status").text(node.status)
        .style("color", getNodeColor(node.status));
    d3.select("#stat-load-fill").transition().duration(500).style("width", node.load + "%");
    d3.select("#stat-datacenters").text(node.datacenters);
}

// UI Handlers
d3.select(".close-btn").on("click", () => {
    d3.select("#overlay-panel").classed("hidden", true);
});

d3.select("#find-node-btn").on("click", function() {
    const btn = d3.select(this);
    const input = d3.select("#location-input").property("value");
    
    if (!input) return;

    btn.text("SCANNING...").property("disabled", true);

    // Simulate network scan
    setTimeout(() => {
        const randomNode = config.nodes[Math.floor(Math.random() * config.nodes.length)];
        highlightRoute(randomNode);
        btn.text("OPTIMIZE ROUTE").property("disabled", false);
    }, 1500);
});

function highlightRoute(node) {
    // Simulate user location (e.g., somewhere in mid-west US if searching from US)
    const userCoords = [-100, 40]; 
    const userProj = projection(userCoords);
    const nodeProj = projection(node.coords);

    // Remove existing user markers
    g.selectAll(".user-marker").remove();
    g.selectAll(".user-route").remove();

    // User marker
    g.append("circle")
        .attr("class", "user-marker user-marker-pulse")
        .attr("cx", userProj[0])
        .attr("cy", userProj[1])
        .attr("r", 4)
        .style("fill", "var(--primary-color)")
        .style("opacity", 0.3);

    g.append("circle")
        .attr("class", "user-marker")
        .attr("cx", userProj[0])
        .attr("cy", userProj[1])
        .attr("r", 3)
        .style("fill", "var(--primary-color)");

    g.append("text")
        .attr("class", "user-marker")
        .attr("x", userProj[0])
        .attr("y", userProj[1] - 10)
        .attr("text-anchor", "middle")
        .style("font-size", "10px")
        .style("font-weight", "bold")
        .text("YOUR LOCATION");

    // Route line
    const d = `M${userProj[0]},${userProj[1]}L${nodeProj[0]},${nodeProj[1]}`;
    g.append("path")
        .attr("class", "user-route")
        .attr("d", d)
        .style("fill", "none")
        .style("stroke", "var(--primary-color)")
        .style("stroke-width", "2px")
        .style("stroke-dasharray", "5,5")
        .style("opacity", 0)
        .transition()
        .duration(1000)
        .style("opacity", 1);

    // Zoom to both
    const bounds = [userProj, nodeProj];
    const x0 = Math.min(userProj[0], nodeProj[0]) - 50;
    const x1 = Math.max(userProj[0], nodeProj[0]) + 50;
    const y0 = Math.min(userProj[1], nodeProj[1]) - 50;
    const y1 = Math.max(userProj[1], nodeProj[1]) + 50;

    svg.transition().duration(1000).call(
        zoom.transform,
        d3.zoomIdentity
            .translate(width / 2, height / 2)
            .scale(Math.min(8, 0.9 / Math.max((x1 - x0) / width, (y1 - y0) / height)))
            .translate(-(x0 + x1) / 2, -(y0 + y1) / 2)
    );
    
    showDetails(node);
}

// Window resize
window.addEventListener('resize', () => {
    const newWidth = container.clientWidth;
    const newHeight = container.clientHeight;
    svg.attr("viewBox", `0 0 ${newWidth} ${newHeight}`);
});

// Simulate traffic updates
setInterval(() => {
    const traffic = (20 + Math.random() * 10).toFixed(1);
    d3.select("#global-traffic").text(`${traffic} TB/s`);
}, 3000);
