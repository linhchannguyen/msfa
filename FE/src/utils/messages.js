const MessageList = {
  MSFA0001() {
    // return `${field}を入力してください。`;
    return '入力してください。';
  },
  MSFA0012(field, max) {
    return `${field}は${max}文字以内で入力してください。`;
  },
  MFSA0020(size = '2MB') {
    return `ファイルサイズが大きすぎます。アップロードできるファイルは、最大${size}バイト以内でお願いします`;
  },
  MSFA0038(extenFile = '「.jpeg」、「.jpg」、「.jpe」、「.png」') {
    return `拡張子が${extenFile}のファイルのみ有効です。`;
  },
  MSFA0039(size = '2MB') {
    return `画像のファイルサイズが上限（最大${size}）を超えています。`;
  },
  MSFA0040() {
    // return `${field}を選択してください。`;
    return '選択してください。';
  },
  MSFA0047() {
    return 'パスワードは8以上25以下の文字半角英数字又は半角記号で入力してください。';
  },
  MSFA0048() {
    return '正常に保存しました。';
  },
  MSFA0049() {
    return '正常に更新しました。';
  },
  MSFA0050() {
    return '正常に削除しました。';
  },
  MSFA0078(field, max) {
    return `${field}は${max}文字以下にしてください。`;
  },
  MSFA0106(field = '') {
    return `${field}は既に存在します。 別の${field}を入力してください。`;
  },
  MSFA0121(users = 'A、B、C') {
    return `${users}は既に存在します。 別のユーザーを選択してください。`;
  },
  MSFA0122(document = 'document') {
    return `${document}は既に存在します。 別の資料を選択してください。`;
  },
  MSFA0123() {
    return '自分のアカウントでは追加できません。異なるユーザーを選択してください。';
  },
  MSFA0131() {
    return '資材を更新できません。新規のカスタム資材として登録しますか。';
  },
  MSFA0133() {
    return '以下のアイテムを完全に削除しますか?';
  },
  MSFA0140(field, max) {
    return `${field}会の添付できるファイル上限数 は${max}にしてください。`;
  }
};

export default MessageList;
