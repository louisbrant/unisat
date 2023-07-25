
window.onload = function () {
    if (window.unisat != undefined && document.querySelector('#login-btn') != undefined) {
        document.querySelector('#login-btn').text = 'Connect wallet';
    }
    if (document.querySelector('#i-invalid-plan')) {
        if (document.querySelector('#i-invalid-plan')?.value?.length) {
            alert('Insufficient balance. ' + document.querySelector('#i-invalid-plan').value);
        }
    }
}

document.querySelector('#login-btn') && document.querySelector('#login-btn').addEventListener('click', async function (e) {
    e.preventDefault();

    //Check the unisat wallet
    if (!window.unisat) {
        window.open('https://unisat.io/');
        return;
    }
    else {
        try {
            await window.unisat.requestAccounts();
            try {
                let address = await window.unisat.getAccounts();
                if (address.length) {
                    try {
                        //address : wallet address , ticker:token name
                        getTokenInfor({ address: address[0], ticker: 'BISO' }, async (result) => {
                            if (result?.status == 1 && result?.result?.tokenBalance) {
                                // let tokenBalance = result.result.tokenBalance.transferableBalance;
                                let tokenBalance = 1000;
                                if (document.querySelector("#i-email")) {
                                    document.querySelector("#i-email").value = address[0];
                                    document.querySelector("#i-balance").value = tokenBalance;
                                    document.getElementById('unisat-wallet-form').submit();
                                }
                            }
                            else {
                                alert("Can't connect Unisat.com!");
                            }
                        })
                    } catch (e) {
                        console.log(e);
                    }
                }
            } catch (e) {
                console.log(e);
            }
        } catch (e) {
        }
    }
});
function getTokenInfor({ address, ticker }, callback) {
    $.ajax({
        url: 'https://unisat.io/api/v3/brc20/token-summary',
        method: 'GET',
        headers: {
            'X-Client': 'UniSat Wallet',
            'Content-Type': 'application/json'
        },

        mode: 'cors',
        cache: 'default',
        data: {
            address,
            ticker
        },
        success: function (response) {
            callback(response);
        },
        error: function (xhr, status, error) {
            if (error?.status != 200) {
                console.log(error);
                callback({ status: 0 });
            }
        }
    });
}