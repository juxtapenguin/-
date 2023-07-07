const { app, BrowserWindow } = require('electron');
const mysql = require('mysql')
const connection = mysql.createConnection({
  
})

// 初期化処理完了イベント
app.on('ready', () => {
  const wnd = new BrowserWindow({ width: 1920, height: 1080 });
  // 画面ファイル読み込み
  wnd.loadFile('main.html');
});
// 全ウィンドウクローズイベント
app.on('window-all-closed', () => app.quit());