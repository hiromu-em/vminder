import * as sidePanel from './ReminderPanel.js';

const SLIDEBAR_BREAKPOINT = '(max-width: 768px)';
const PULLDOWN_BREAKPOINT = '(max-width: 500px)';

const checkboxes = document.querySelectorAll('.member-card input[type="checkbox"]');
const sidebarList = document.getElementById('sidebar-list');
const searchBox = document.getElementById('member-search-box');
const memberCards = document.querySelectorAll('.member-card');
const groupsButton = document.querySelectorAll('.group-select-button');
const groupTitle = document.querySelectorAll('.group-title');
const topButton = document.querySelector('.top-bak-container button');
const resetButton = document.getElementById('reset-all-button');
const groupContainer = document.querySelector('.group-container');
const mainContentWrapper = document.querySelector('.main-content-wrapper');
const fadeOutRemoveContainer = document.querySelector('.fade-out-remove-container');
const memberSearch = document.querySelector('.member-search');

const sideObject = new sidePanel.ReminderPanel();
const slidebarMediaQuery = window.matchMedia(SLIDEBAR_BREAKPOINT);
const pullDownMediaQuery = window.matchMedia(PULLDOWN_BREAKPOINT);

document.body.addEventListener('click', (e) => {
    const slidebarElements = sideObject.getSlidebarElements();
    const slidebarContainer = slidebarElements.container;
    const slideOpenButton = slidebarElements.openButton;
    const removeButtons = slidebarElements.removeButtons ?? [];

    const findItem = removeButtons.find(item => item.contains(e.target));

    if (typeof findItem !== 'undefined') {
        return;
    }

    if (slideOpenButton && slidebarContainer) {
        if (!slideOpenButton.contains(e.target) && !slidebarContainer.contains(e.target)) {

            slidebarContainer.style.transform = 'translate(265px)';
            slideOpenButton.style.opacity = '1';
            slideOpenButton.style.right = '0';
        }
    }
});

const groupOffsets = [
    { value: 'hololive', text: 'ホロライブ' },
    { value: 'holostars', text: 'ホロスターズ' },
    { value: 'nizisanzi', text: 'にじさんじ' },
    { value: 'vspo', text: 'ぶいすぽっ！' }
];

function scrollToGroup(targetValueOrText, offset = 75){
    const index = groupOffsets.findIndex(
        group => group.value === targetValueOrText || group.text === targetValueOrText
    );
    if(index !== -1){
        window.scrollTo({ top: groupTitle.item(index).offsetTop - offset, behavior: 'instant' });
    }
}

function scrollEvent(e) {
    const value = e.target.value || e.target.textContent;
    const offset = e.target.className === 'pull-down-list' ? 145 : 75;
    scrollToGroup(value, offset);
    
}

/**
 * グループ選択プルダウンメニューをページに追加
 */
function createPullDown() {

    const groupContainer = document.querySelector('.group-container');
    const pullDownList = document.createElement('select');
    pullDownList.className = 'pull-down-list';
    const pullDownOptions = [
        { value: '', text: 'グループ選択' },
        { value: 'hololive', text: 'ホロライブ' },
        { value: 'holostars', text: 'ホロスターズ' },
        { value: 'nizisanzi', text: 'にじさんじ' },
        { value: 'vspo', text: 'ぶいすぽっ！' }
    ];

    pullDownOptions.forEach(optionData => {
        const option = document.createElement('option');
        option.value = optionData.value;
        option.text = optionData.text;
        pullDownList.appendChild(option);
    });

    pullDownList.addEventListener('change', scrollEvent);
    groupContainer.appendChild(pullDownList);
    groupContainer.style.padding = '45px 0 0';
}

/**
 * グループ選択プルダウンメニューをページから削除
 */
function removePullDown() {

    const pullDownList = groupContainer.querySelector('.pull-down-list');

    if (pullDownList) {

        pullDownList.remove();
        groupContainer.style.padding = ''
    }
}

/**
 * プルダウン表示用メディアクエリの状態変化ハンドラ
 */
function handlePullDownMediaQueryChange(event) {
    if (event.matches) {
        createPullDown();
    } else {
        removePullDown();
    }
}

// プルダウン初期表示とリスナー設定
handlePullDownMediaQueryChange(pullDownMediaQuery);
pullDownMediaQuery.addEventListener('change', handlePullDownMediaQueryChange);


/**
 * チェックボックスの状態に基づいてサイドバー/スライドバーを更新する
 */
function updatePanels() {

    sideObject.updatePanel(checkboxes, slidebarMediaQuery);
}

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updatePanels);
});

/**
 * サイドバー/スライドバー内の削除ボタンクリックイベントハンドラ
 * @param {Event} event - クリックイベント
 */
function handleRemoveButtonClick(event) {

    const removeButton = event.target.closest('.remove-button');

    // 削除ボタンがクリックされ、かつ data-member-id が存在する場合
    if (removeButton && removeButton.dataset.memberId) {
        const memberIdToUncheck = removeButton.dataset.memberId;

        const checkboxToUncheck = document.querySelector(`.member-card input[type="checkbox"][value="${memberIdToUncheck}"]`);

        if (checkboxToUncheck) {

            checkboxToUncheck.checked = false;
            checkboxToUncheck.dispatchEvent(new Event('change'));
        }
    }
}

if (sidebarList) {
    sidebarList.addEventListener('click', handleRemoveButtonClick);
}

if (mainContentWrapper) {
    mainContentWrapper.addEventListener('click', (event) => {

        const slidebarListElement = sideObject.getSlidebarElements().list;

        if (slidebarListElement && slidebarListElement.contains(event.target)) {
            handleRemoveButtonClick(event);
        }
    });
}

searchBox.addEventListener('input', () => {
    const searchTerm = searchBox.value.trim().replace(/\s+/g, '');

    memberCards.forEach(card => {

        const memberHiraName = card.querySelector('span[data-kana]');
        const memberName = memberHiraName.dataset.kana.replace(/\s+/g, '');

        if (memberName.includes(searchTerm) || wanakana.toKatakana(memberName).includes(searchTerm) || card.textContent.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});


topButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'instant'
    });
});

groupsButton.forEach(groupButton => {
    groupButton.addEventListener('click', scrollEvent);
});

resetButton.addEventListener('click', () => {
    let changed = false;
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.checked = false;
            changed = true;
        }
    });

    if (changed) {
        updatePanels();
    }
});

window.matchMedia(PULLDOWN_BREAKPOINT).addEventListener('change', () => {
    memberSearch.style = '';
    searchBox.style = '';
});

//登録完了 or メンバーを選択してくださいエラー
if (fadeOutRemoveContainer) {
    setTimeout(() => {
        fadeOutRemoveContainer.style.transition = 'opacity 0.5s ease-out';
        fadeOutRemoveContainer.style.opacity = '0';
        setTimeout(() => { fadeOutRemoveContainer.remove(); }, 500);
    }, 2200);
}