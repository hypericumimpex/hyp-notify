<?php

$gdpr_statement = '
<h1>Privacy Policy</h1>
<p>Effective date: '.date('M d, Y').'</p>

<p>'.get_bloginfo('name').' ("us", "we", or "our") operates the '.get_bloginfo('wpurl').' website (the "Service").</p>
<p>This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data.</p>
<p>We use your data to provide and improve the Service. By using the Service, you agree to the collection and use of information in accordance with this policy. Unless otherwise defined in this Privacy Policy, terms used in this Privacy Policy have the same meanings as in our Terms and Conditions, accessible from '.get_bloginfo('wpurl').'</p>

<h2>Information Collection And Use</h2>
<p>We collect several different types of information for various purposes to provide and improve our Service to you.</p>

<h3>Types of Data Collected</h3>

<h4>Personal Data</h4>
<p>While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). Personally identifiable information may include, but is not limited to:</p>
<ul>
  <li>Email address</li>
  <li>First name and last name</li>
  <li>Web push browser token</li>
  <li>Facebook Messenger user ID</li>
  <li>Facebook notifications user ID</li>
  <li>Country</li>
  <li>City</li>
  <li>Language</li>
  <li>Cookies and Usage Data</li>
</ul>

<h4>Usage Data</h4>
<p>We may also collect information how the Service is accessed and used ("Usage Data"). This Usage Data may include information such as your computer\'s Internet Protocol address(e . g . IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data .</p>

<h4>Tracking & Cookies Data</h4>
<p>We use cookies and similar tracking technologies to track the activity on our Service and hold certain information .</p>
<p>Cookies are files with small amount of data which may include an anonymous unique identifier . Cookies are sent to your browser from a website and stored on your device . Tracking technologies also used are beacons, tags, and scripts to collect and track information and to improve and analyze our Service .</p>
<p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent . However, if you do not accept cookies, you may not be able to use some portions of our Service .</p>
<p>Cookies we use:</p>
<ul>
  <li><strong>Notification popup request .</strong > We use this cookie to not display notification popup box again when user choose to dismiss it .</li>
  <li><strong>Safari browser token .</strong > We use this cookie to store the user token of his Safari browser for web push service if user allow to us a permission to sending him notifications .</li>
  <li><strong>Browser token .</strong > We use this cookie to store the user token of his browser for web push service if user allow to us a permission to sending him notifications .</li>
  <li><strong>Welcome message seen .</strong > We use this cookie to know if user already see the welocme message of web push notification service or not .</li>
  <li><strong>User profile link status .</strong > We use this cookie to know if user WordPress account is already linked with web push browser token or not .</li>
  <li><strong>GPS coordinates .</strong > We use two cookies for GPS coordinates to store user current GPS location updated every hour if user give us a permission to read browser GPS location .</li>
  <li><strong>Session Cookies .</strong > We use Session Cookies to operate our Service .</li>
  <li><strong>Preference Cookies .</strong > We use Preference Cookies to remember your preferences and various settings .</li>
  <li><strong>Security Cookies .</strong > We use Security Cookies for security purposes .</li>
</ul >

<h2>Use of Data </h2>
<p>'.get_bloginfo('name').' uses the collected data for various purposes:</p>
<ul>
  <li>To send you notifications about our recent offers, news, blogs, products or alerts</li>
  <li>To provide and maintain the Service</li>
  <li>To notify you about changes to our Service</li>
  <li>To allow you to participate in interactive features of our Service when you choose to do so</li>
  <li>To provide customer care and support</li>
  <li>To provide analysis or valuable information so that we can improve the Service</li>
  <li>To monitor the usage of the Service</li>
  <li>To detect, prevent and address technical issues</li>
</ul>

<h2>Your Choices and Opt-Outs</h2>
<p>Members and Visitors who have opted in to our marketing tools can opt out of receiving any notifications from us at any time by</p>
<ul>
  <li>Clicking the "unsubscribe" link at the bottom of our marketing messages</li>
  <li>Sending "'.self::$apisetting['msn_unsubs_command'].'" to our Facebook Messenger</li>
  <li>Clicking on the bell icon at the bottom page to unsubscribe from web push notifications</li>
  <li>Visiting the subscription page to manage your notifications preferences or permanently deleting your subscription</li>
</ul>

<h2>Transfer Of Data</h2>
<p>Your information, including Personal Data, may be transferred to — and maintained on — computers located outside of your state, province, country or other governmental jurisdiction where the data protection laws may differ than those from your jurisdiction.</p>
<p>If you are located outside United States and choose to provide information to us, please note that we transfer the data, including Personal Data, to United States and process it there.</p>
<p>Your consent to this Privacy Policy followed by your submission of such information represents your agreement to that transfer.</p>
<p>'.get_bloginfo('name').' will take all steps reasonably necessary to ensure that your data is treated securely and in accordance with this Privacy Policy and no transfer of your Personal Data will take place to an organization or a country unless there are adequate controls in place including the security of your data and other personal information.</p>

<h2>Disclosure Of Data</h2>

<h3>Legal Requirements</h3>
<p>'.get_bloginfo('name').' may disclose your Personal Data in the good faith belief that such action is necessary to:</p>
<ul>
  <li>To comply with a legal obligation</li>
  <li>To protect and defend the rights or property of '.get_bloginfo('name').'</li>
  <li>To prevent or investigate possible wrongdoing in connection with the Service</li>
  <li>To protect the personal safety of users of the Service or the public</li>
  <li>To protect against legal liability</li>
</ul>

<h2>Security Of Data</h2>
<p>The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

<h2>Other Data Protection Rights</h2>
<p>You have the following data protection rights:</p>
<ul>
  <li>To access, correct, update, or request deletion of your Personal Information. '.get_bloginfo('name').' takes reasonable steps to ensure that the data we collect is reliable for its intended use, accurate, complete, and up to date. You may contact us directly at any time about accessing, correcting, updating, or deleting your Personal Information, or altering your data or marketing preferences by emailing us at '.get_bloginfo('admin_email').'. We will consider your request in accordance with applicable laws</li>
  <li>In addition, if you are a resident of the EEA, you can object to processing of your Personal Information, ask us to restrict processing of your Personal Information or request portability of your Personal Information. Again, you can exercise these rights by emailing us at '.get_bloginfo('admin_email').'</li>
  <li>Similarly, if we have collected and processed your Personal Information with your consent, then you can withdraw your consent at any time. Withdrawing your consent will not affect the lawfulness of any processing we conducted prior to your withdrawal, nor will it affect processing of your Personal Information conducted in reliance on lawful processing grounds other than consent</li>
  <li>You have the right to complain to a data protection authority about our collection and use of your Personal Information. For more information, please contact your local data protection authority. Contact details for data protection authorities in the EEA are available <a href="http://ec.europa.eu/justice/data-protection/article-29/structure/data-protection-authorities/index_en.htm" target="_blank">here</a> </li>
  <li></li>
  <li></li>
</ul>
<p>We respond to all requests we receive from individuals wishing to exercise their data protection rights in accordance with applicable data protection laws. We may ask you to verify your identity in order to help us respond efficiently to your request.</p>

<h2>Service Providers</h2>
<p>We may employ third party companies and individuals to facilitate our Service ("Service Providers"), to provide the Service on our behalf, to perform Service-related services or to assist us in analyzing how our Service is used.</p>
<p>These third parties have access to your Personal Data only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>

<h3>Analytics</h3>
<p>We may use third-party Service Providers to monitor and analyze the use of our Service.</p>
<ul>
  <li>
    <p><strong>Google Analytics</strong></p>
    <p>Google Analytics is a web analytics service offered by Google that tracks and reports website traffic. Google uses the data collected to track and monitor the use of our Service. This data is shared with other Google services. Google may use the collected data to contextualize and personalize the ads of its own advertising network.</p>
    <p>You can opt-out of having made your activity on the Service available to Google Analytics by installing the Google Analytics opt-out browser add-on. The add-on prevents the Google Analytics JavaScript (ga.js, analytics.js, and dc.js) from sharing information with Google Analytics about visits activity.</p>                <p>For more information on the privacy practices of Google, please visit the Google Privacy & Terms web page: <a href="https://policies.google.com/privacy?hl=en">https://policies.google.com/privacy?hl=en</a></p>
  </li>
</ul>

<h2>Links To Other Sites</h2>
<p>Our Service may contain links to other sites that are not operated by us. If you click on a third party link, you will be directed to that third party\'s site. We strongly advise you to review the Privacy Policy of every site you visit.</p>
<p>We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services.</p>

<h2>Changes To This Privacy Policy</h2>
<p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
<p>We will let you know via email and/or a prominent notice on our Service, prior to the change becoming effective and update the "effective date" at the top of this Privacy Policy.</p>
<p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>

<h2>Contact Us</h2>
<p>If you have any questions about this Privacy Policy, please contact us:</p>
<ul>
  <li>By visiting this page on our website: '.get_bloginfo('wpurl').'/contact</li>
  <li>By sending email to: '.get_bloginfo('admin_email').'</li>
</ul>
';

echo htmlspecialchars($gdpr_statement);