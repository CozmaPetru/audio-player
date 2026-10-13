// Playerul audio din partea de jos 
const player = document.getElementById("player");

// arată ce melodie se redă  
const label = document.getElementById("now-playing");

const buttons = [...document.querySelectorAll(".play-btn")];

// Coada de redare: lista melodiilor care vor fi cântate, una după alta
let queue = [],
    index = 0;
function playAt(i) {
    if (!queue[i]) return;
    index = i;
    player.src = queue[i].dataset.src;
    label.textContent = queue[i].dataset.title;
    player.play();
}


buttons.forEach((btn) =>
    btn.addEventListener("click", () => {
       

        if (btn.dataset.list === "playlist") {
           
            queue = buttons.filter((b) => b.dataset.list === "playlist");
            playAt(queue.indexOf(btn));
        } else {
           
            queue = [btn];

        
            playAt(0);
        }
    }),
);

player.addEventListener("ended", () => playAt(index + 1));