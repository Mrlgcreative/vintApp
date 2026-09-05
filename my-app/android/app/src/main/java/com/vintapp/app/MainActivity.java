package com.vintapp.app;

import android.os.Bundle;
import android.webkit.WebSettings;
import com.getcapacitor.BridgeActivity;

public class MainActivity extends BridgeActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        fixWebViewSettings();
    }

    @Override
    public void onResume() {
        super.onResume();
        fixWebViewSettings();
    }

    private void fixWebViewSettings() {
        try {
            android.webkit.WebView webView = getBridge().getWebView();
            WebSettings settings = webView.getSettings();
            settings.setUseWideViewPort(true);
            settings.setLoadWithOverviewMode(true);
            settings.setTextZoom(100);
        } catch (Exception e) {
            // WebView pas encore prête
        }
    }
}
