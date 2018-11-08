export default function editorTabs() {

  document.addEventListener("DOMContentLoaded", () => {
    const tabLinks = document.querySelectorAll('.editor__tabbar a[data-toggle="pill"]');
    let url = location.href.replace(/\/$/, "");

    if (location.hash) {
      const hash = url.split("#");
      $('.editor__tabbar a[href="#'+hash[1]+'"]').tab("show");
      url = location.href.replace(/\/#/, "#");
      history.replaceState(null, null, url);
    } 
    
    Array.from(tabLinks).forEach(tab => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();
        generateHash(e.target.getAttribute("href"))
      });
    });

    function generateHash(hash) {
      let newHash;
      newHash = url.split("#")[0] + hash;
      history.replaceState(null, null, newHash);
    }

  });
  
}
