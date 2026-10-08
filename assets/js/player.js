const player = document.getElementById("player");
const label = document.getElementById("now-playing");
const buttons = [...document.querySelectorAll(".play-btn")];
let queue = [],
    index = 0;

function playAt(i) {
    if (!queue[i]) return;
    index = i;
    player.src = queue[i].dataset.src;
    label.textContent = "▶ " + queue[i].dataset.title;
    player.play();
}

buttons.forEach((btn) =>
    btn.addEventListener("click", () => {
        if (btn.dataset.list === "pl") {
            queue = buttons.filter((b) => b.dataset.list === "pl"); // redăm tot playlist-ul în ordine
            playAt(queue.indexOf(btn));
        } else {
            queue = [btn];
            playAt(0);
        }
    }),
);

player.addEventListener("ended", () => playAt(index + 1)); // următoarea melodie
