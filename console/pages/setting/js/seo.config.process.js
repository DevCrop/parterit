/* SEO 관리 — 다국어 메타 / 파비콘 / 사이트맵 */

(function () {
  function postForm(mode, opts) {
    opts = opts || {};
    var form = document.getElementById("frm");
    var data = new FormData(form);

    // 사이트맵 모드일 때는 파일 필드를 제어 (다른 파일 같이 안 보냄)
    if (mode === "sitemap.upload") {
      var only = new FormData();
      var f = document.getElementById("sitemap_xml");
      if (f && f.files && f.files[0]) {
        only.append("sitemap_xml", f.files[0]);
      }
      only.append("mode", mode);
      data = only;
    } else if (mode === "sitemap.generate") {
      var fd = new FormData();
      fd.append("mode", mode);
      data = fd;
    } else {
      data.set("mode", mode);
    }

    return $.ajax({
      type: "POST",
      url: "./ajax/seo.config.process.php",
      data: data,
      processData: false,
      contentType: false,
      cache: false,
    });
  }

  function bindSitemapButtons() {
    var btnGen = document.getElementById("btnSitemapGenerate");
    if (btnGen) {
      btnGen.addEventListener("click", function () {
        if (!confirm("라우터를 스캔하여 sitemap.xml 을 새로 생성합니다. 계속하시겠습니까?")) {
          return;
        }
        btnGen.disabled = true;
        postForm("sitemap.generate")
          .done(function (resp) {
            try {
              var json = typeof resp === "string" ? JSON.parse(resp) : resp;
              alert(json.msg || "완료");
              if (json.result === "success") location.reload();
            } catch (e) {
              alert("응답 파싱 실패");
            }
          })
          .fail(function () {
            alert("요청 실패");
          })
          .always(function () {
            btnGen.disabled = false;
          });
      });
    }

    var btnUp = document.getElementById("btnSitemapUpload");
    if (btnUp) {
      btnUp.addEventListener("click", function () {
        var input = document.getElementById("sitemap_xml");
        if (!input || !input.files || !input.files[0]) {
          alert("업로드할 sitemap.xml 파일을 선택해주세요.");
          return;
        }
        btnUp.disabled = true;
        postForm("sitemap.upload")
          .done(function (resp) {
            try {
              var json = typeof resp === "string" ? JSON.parse(resp) : resp;
              alert(json.msg || "완료");
              if (json.result === "success") location.reload();
            } catch (e) {
              alert("응답 파싱 실패");
            }
          })
          .fail(function () {
            alert("요청 실패");
          })
          .always(function () {
            btnUp.disabled = false;
          });
      });
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    bindSitemapButtons();
  });
})();

function doSeoConfigSave() {
  document.getElementById("mode").value = "seo.config.save";

  var form = document.getElementById("frm");
  var data = new FormData(form);
  // sitemap_xml 파일은 저장 시 함께 보내지 않음 (별도 업로드 버튼 사용)
  if (data.has("sitemap_xml")) {
    data.delete("sitemap_xml");
  }

  $.ajax({
    type: "POST",
    url: "./ajax/seo.config.process.php",
    data: data,
    processData: false,
    contentType: false,
    cache: false,
    success: function (resp) {
      try {
        var json = typeof resp === "string" ? JSON.parse(resp) : resp;
        alert(json.msg || (json.result === "success" ? "저장되었습니다." : "실패"));
        if (json.result === "success") location.reload();
      } catch (e) {
        alert("응답 파싱 실패");
      }
    },
    error: function () {
      alert("요청 실패");
    },
  });
}
