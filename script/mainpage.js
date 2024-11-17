const confirmButton = document.getElementById("confirm");
const investMoneyTextElement = document.getElementById("investMoneyText");
var welfareButton = document.querySelector('.card-body button');

function purchase() {
  confirmButton.disabled = false;

  const purchaseButton = document.activeElement;
  const cardElement = purchaseButton.parentElement;

  if (!cardElement) {
    console.error("Card element not found!");
    return;
  }

  const textElement = cardElement.querySelector(".card-text");
  const text = textElement ? textElement.innerHTML : "";

  const investMoneyPattern = /Invest Money: ₹(\d+(,\d+)*)/;
  const incomeDailyPattern = /Income Daily: ₹(\d+(,\d+)*)/;
  const incomeDaysPattern = /Income Days: (\d+)/;
  const giftPattern = /Gift:VIP Level (\d+)/;
  const reqgiftPattern = /Requirement: VIP Level (\d+)/;

  const investMoneyMatch = text.match(investMoneyPattern);
  const incomeDailyMatch = text.match(incomeDailyPattern);
  const incomeDaysMatch = text.match(incomeDaysPattern);
  const giftMatch = text.match(giftPattern);
  const reqgiftMatch = text.match(reqgiftPattern);

  const investMoney = investMoneyMatch ? investMoneyMatch[1] : "";
  const incomeDaily = incomeDailyMatch ? incomeDailyMatch[1] : "";
  const incomeDays = incomeDaysMatch ? incomeDaysMatch[1] : "";
  const gift = giftMatch ? giftMatch[1] : "";
  const reqgift = reqgiftMatch ? reqgiftMatch[1] : "";

  return {
    investMoney,
    incomeDaily,
    incomeDays,
    gift,
    reqgift,
  };
}

function stable() {
  const purchaseInfo = purchase();

  investMoneyTextElement.innerHTML = `Income: Stable<br>Invest Money: ₹${purchaseInfo.investMoney}<br>Income Daily: ₹${purchaseInfo.incomeDaily}<br>Income Days: ${purchaseInfo.incomeDays}<br>Gift: VIP Level ${purchaseInfo.gift}<br>`;

  const purchaseModal = new bootstrap.Modal(
    document.getElementById("purchaseModal")
  );
  purchaseModal.show();
}

function welfare() {
  const purchaseInfo = purchase();
  investMoneyTextElement.innerHTML = `Income: VIP (Welfare & Activity)<br>Invest Money: ₹${purchaseInfo.investMoney}<br>Income Daily: ₹${purchaseInfo.incomeDaily}<br>Income Days: ${purchaseInfo.incomeDays}<br>Requirement: VIP Level ${purchaseInfo.reqgift}`;

  fetch("php/get_balance.php")
    .then((response) => {
      if (response.ok) {
        return response.json();
      } else {
        throw new Error("Network response was not OK.");
      }
    })
    .then((data) => {
      if (data.success) {
        const gift = data.gift;
        const invest_money = data.invest_money.map(Number); // Convert invest_money array elements to Floategers

        const cleanedInvestMoney = parseFloat(purchaseInfo.investMoney.replace(/,/g, '')); //commas removed

        if (invest_money.includes(cleanedInvestMoney)) {
          throw new Error("Already Purchased This Product! Please wait until the product's expiration before making another purchase.");
        } else {
          if (gift.includes(purchaseInfo.reqgift)) {
            welfareButton.disabled = false;
            confirmButton.disabled = false;
          } else {
            throw new Error(`Purchase Stable Product of level: ${purchaseInfo.reqgift}`);
          }
        }

        const purchaseModal = new bootstrap.Modal(
          document.getElementById("purchaseModal")
        );
        purchaseModal.show();
      }

    })
    .catch((error) => {
      confirmButton.disabled = true;
      const investMoneyTextElement = document.getElementById("investMoneyText");
      investMoneyTextElement.textContent = `${error.message}`;

      const purchaseModal = new bootstrap.Modal(
        document.getElementById("purchaseModal")
      );
      purchaseModal.show();
    });
}

function confirmPurchase() {
  confirmButton.disabled = true;

  const text = investMoneyTextElement.textContent;
  const investMoneyPattern = /Invest Money: ₹(\d+(,\d+)*)/;
  const investMoneyMatch = text.match(investMoneyPattern);
  const investMoneyString = investMoneyMatch ? investMoneyMatch[1] : "";
  const investMoney = parseFloat(investMoneyString.replace(/,/g, ""));

  let product, incomeDaily, incomeDays, gift = 0;

  //define all products here
  switch (investMoney) {
    case 477:
      product = "Stable Income 1";
      incomeDaily = 150;
      incomeDays = 35;
      gift = 1;
      break;
    case 1400:
      product = "Stable Income 2";
      incomeDaily = 546;
      incomeDays = 35;
      gift = 2;
      break;
    case 3700:
      product = "Stable Income 3";
      incomeDaily = 1461;
      incomeDays = 35;
      gift = 3;
      break;
    case 8000:
      product = "Stable Income 4";
      incomeDaily = 3800;
      incomeDays = 35;
      gift = 4;
      break;
    case 16000:
      product = "Stable Income 5";
      incomeDaily = 7000;
      incomeDays = 35;
      gift = 5;
      break;
    case 30000:
      product = "Stable Income 6";
      incomeDaily = 14500;
      incomeDays = 35;
      gift = 6;
      break;


    case 200:
      product = "Daily Income 1";
      incomeDaily = 90;
      incomeDays = 3;
      break;
    case 1700:
      product = "Daily Income 2";
      incomeDaily = 700;
      incomeDays = 3;
      break;
    case 4200:
      product = "Daily Income 3";
      incomeDaily = 2100;
      incomeDays = 3;
      break;
    case 9000:
      product = "Daily Income 4";
      incomeDaily = 3500;
      incomeDays = 3;
      break;
    case 15000:
      product = "Daily Income 5";
      incomeDaily = 8000;
      incomeDays = 3;
      break;
    case 28000:
      product = "Daily Income 6";
      incomeDaily = 13500;
      incomeDays = 3;
      break;


    case 1000:
      product = "Cumulative Income 1";
      incomeDaily = 1500;
      incomeDays = 10;
      break;
    case 3000:
      product = "Cumulative Income 2";
      incomeDaily = 4500;
      incomeDays = 10;
      break;
    case 8100:
      product = "Cumulative Income 3";
      incomeDaily = 12000;
      incomeDays = 10;
      break;


    default:
      throw new Error(`[NOT MENTIONED IN SWICH]Invalid income type for stable.`);
  }

  const total_income = incomeDaily * incomeDays;

  fetch("php/get_balance.php")
    .then((response) => {
      if (response.ok) {
        return response.json();
      } else {
        throw new Error("Network response was not OK.");
      }
    })
    .then((data) => {
      if (data.success) {
        const recharge = parseFloat(data.recharge, 10);


        if (recharge < investMoney) {
          investMoneyTextElement.innerHTML = `Insufficient balance, Rechage and try again!`;
        } else {
          try {
            if (gift !== 0) { //if gift==0 it means it is welfare or cumuilative product so i take "!" to stable
              invite(investMoney);  // send money to inviters
              rewards();
            }

            const newBalance = recharge - investMoney;
            investMoneyTextElement.innerHTML = `PURCHASE SUCCESSFUL!`;

            console.log(recharge);
            console.log(newBalance);


            fetch("php/update_balance.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ recharge: newBalance }),
            })
              .then((response) =>
                response.ok
                  ? response.json()
                  : Promise.reject(new Error("Network response was not OK."))
              )
              .then((data) => {
                if (!data.success) console.error("Error:", data.message);
              })
              .catch((error) => {
                investMoneyTextElement.textContent = error.message;
              });

            fetch("php/purchase.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                product,
                investMoney,
                incomeDaily,
                incomeDays,
                total_income,
                gift,
              }),
            })
              .then((response) =>
                response.ok
                  ? response.json()
                  : Promise.reject(new Error("Network response was not OK."))
              )
              .then((data) => {
                if (!data.success) console.error("Error:", data.message);
              })
              .catch((error) => {
                investMoneyTextElement.textContent = error.message;
              });
          } catch (error) {
            investMoneyTextElement.textContent = error.message;
          }
        }
      } else {
        console.error("Error:", data.message);
      }
    })
    .catch((error) => {
      investMoneyTextElement.textContent = error.message;
    })
}



function invite(investMoney) {
  fetch("php/get_balance.php")
    .then((response) => {
      if (response.ok) {
        return response.json();
      } else {
        throw new Error("Network response was not OK.");
      }
    })
    .then((data) => {
      if (data.success) {
        const presentUserMobile = data.presentUserMobile;
        const level1Mobile = data.level1Mobile;
        const level2Mobile = data.level2Mobile;
        const level3Mobile = data.level3Mobile;
        const balance1 = parseFloat(data.balance1, 10);
        const balance2 = parseFloat(data.balance2, 10);
        const balance3 = parseFloat(data.balance3, 10);
        const byinvite1 = parseFloat(data.byinvite1, 10);
        const byinvite2 = parseFloat(data.byinvite2, 10);
        const byinvite3 = parseFloat(data.byinvite3, 10);

        // Use the retrieved values as needed

        var newBalance1, newBalance2, newBalance3;
        var newbyinvite1, newbyinvite2, newbyinvite3;

        if (level1Mobile !== null) {
          newBalance1 = balance1 + investMoney * 0.2;
          newbyinvite1 = byinvite1 + investMoney * 0.2;
        } else {
          newBalance1 = balance1;
          newbyinvite1 = byinvite1;
        }

        if (level2Mobile !== null) {
          newBalance2 = balance2 + investMoney * 0.03;
          newbyinvite2 = byinvite2 + investMoney * 0.03;
        } else {
          newBalance2 = balance2;
          newbyinvite2 = byinvite2;
        }

        if (level3Mobile !== null) {
          newBalance3 = balance3 + investMoney * 0.02;
          newbyinvite3 = byinvite3 + investMoney * 0.02;
        } else {
          newBalance3 = balance3;
          newbyinvite3 = byinvite3;
        }


        fetch("php/update_invite.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            presentUserMobile: presentUserMobile,
            investMoney: investMoney, // Add investMoney to the request
            level1Mobile: level1Mobile,
            level2Mobile: level2Mobile,
            level3Mobile: level3Mobile,
            balance1: newBalance1,
            balance2: newBalance2,
            balance3: newBalance3,
            byinvite1: newbyinvite1,
            byinvite2: newbyinvite2,
            byinvite3: newbyinvite3,
          }),
        })
          .then((response) =>
            response.ok
              ? response.json()
              : Promise.reject(new Error("Network response was not OK."))
          )
          .then((data) => {
            if (!data.success) console.error("Error:", data.message);
          })
          .catch((error) => {
            investMoneyTextElement.textContent = error.message;
          });

        console.log(presentUserMobile);

        console.log(level1Mobile);
        console.log(balance1);
        console.log(newBalance1);
        console.log(byinvite1);
        console.log(newbyinvite1);

        console.log(level2Mobile);
        console.log(balance2);
        console.log(newBalance2);
        console.log(byinvite2);
        console.log(newbyinvite2);

        console.log(level3Mobile);
        console.log(balance3);
        console.log(newBalance3);
        console.log(byinvite3);
        console.log(newbyinvite3);


      } else {
        // Handle the case when the user is not found
        console.log(data.message + ": " + data.mobile);
        // Send an AJAX request to the PHP script to destroy the session
        fetch('php/logout.php')
          .then(response => {
            // Redirect to the login page
            window.location.href = 'login.html';
          })
          .catch(error => {
            console.error('Logout error:', error);
          });
      }
    })
    .catch((error) => {
      // Handle any errors that occur during the fetch request
      console.log(error);
    });
}

function rewards() {
  fetch('php/rewards.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.text();
    })
    .then(data => {
      // Convert the data to an integer
      const reward = parseInt(data);
      console.log('Reward:', reward);
    })
    .catch(error => {
      console.error('Fetch Error:', error);
    });
}
