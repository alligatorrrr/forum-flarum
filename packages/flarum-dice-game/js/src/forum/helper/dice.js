export default function dice(dice,size = 36,color="var(--primary-color)"){
    let diceText = "";
    switch (dice) {
      case 1: diceText = "one"; break;
      case 2: diceText = "two"; break;
      case 3: diceText = "three"; break;
      case 4: diceText = "four"; break;
      case 5: diceText = "five"; break;
      case 6: diceText = "six"; break;
    }

    return (
        <i style={"font-size:"+size+"px;color:"+color} className={"fas fa-dice-"+diceText+""}></i>
    );
}