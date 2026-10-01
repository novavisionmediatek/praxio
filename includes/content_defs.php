<?php
declare(strict_types=1);
/**
 * Every editable piece of page copy and every page image.
 * Shape: page => ['label' => ..., 'fields' => [key => [type, label, default]]]
 * Types: text | longtext | lines (one item per line) | image.
 * In text fields, wrap words in *asterisks* to show them in the highlight colour.
 * The admin "Page content" screen is built from this list, and c('key') falls back to the default here.
 */
$U = fn(string $id, int $w = 1600, int $q = 70) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w={$w}&q={$q}";

/** Standard banner at the top of an inner page. */
$hero = fn(string $p, string $img, string $title, string $sub) => [
    "{$p}_hero_img"   => ['image', 'Banner image', $img],
    "{$p}_hero_title" => ['text', 'Banner heading', $title],
    "{$p}_hero_sub"   => ['longtext', 'Banner sub-text', $sub],
];
$seo = fn(string $p, string $title, string $desc) => [
    "{$p}_seo_title" => ['text', 'Browser tab title (SEO)', $title],
    "{$p}_seo_desc"  => ['longtext', 'Search-engine description (SEO)', $desc],
];
$section = fn(string $p, string $name, string $eyebrow, string $title) => [
    "{$p}_{$name}_eyebrow" => ['text', ucfirst($name) . ': small label above heading', $eyebrow],
    "{$p}_{$name}_title"   => ['text', ucfirst($name) . ': heading', $title],
];

return [
    'header' => ['label' => 'Logo & top bar', 'section' => 'site', 'fields' => [
        'site_logo_img' => ['image', 'Logo: image (empty = the built-in Praxia logo)', ''],
        'site_logo_height' => ['text', 'Logo: height in pixels', '40'],
        'site_brand_name' => ['text', 'Logo: brand name beside the image (empty = show only the image)', 'PRAXIA'],
        'site_footer_logo_img' => ['image', 'Footer logo: image for the dark footer (empty = use the main logo)', ''],
        'site_favicon' => ['image', 'Favicon: small icon in the browser tab (square, 64×64 or larger)', ''],
        'site_topbar_on' => ['toggle', 'Top bar: show the bar above the menu', '1'],
        'site_topbar_lead' => ['text', 'Top bar: bold text', 'Asset-backed financing'],
        'site_topbar_rest' => ['text', 'Top bar: rest of the text', "for India's mid-market enterprises"],
        'site_topbar_contact' => ['toggle', 'Top bar: show phone and email on the right', '1'],
        'site_cta_on' => ['toggle', 'Menu button: show the highlighted button', '1'],
        'site_cta_label' => ['text', 'Menu button: label', 'Contact Us'],
        'site_cta_url' => ['text', 'Menu button: link', 'contact.php'],
    ]],

    'footer' => ['label' => 'Footer', 'section' => 'site', 'fields' => [
        'site_footer_blurb' => ['longtext', 'About text: shown under the logo', 'Partner of choice for moveable CapEx assets for Indian corporates. Operating leases that keep your balance sheet light.'],
        'site_foot_col1' => ['text', 'Column 1: title', 'Company'],
        'site_foot_col2' => ['text', 'Column 2: title', 'Explore'],
        'site_foot_col3' => ['text', 'Column 3: title', 'Reach us'],
        'site_footer_address' => ['longtext', 'Column 3: address (optional, shown under the phone number)', ''],
        'site_footer_copy' => ['text', 'Bottom bar: copyright text ({year} and {company} fill in automatically)', '© {year} Praxia ({company}). All rights reserved.'],
        'site_domain' => ['text', 'Bottom bar: right-hand text', 'praxia.co.in'],
        'site_social_linkedin' => ['text', 'Social links: LinkedIn address (empty = hidden)', ''],
        'site_social_x' => ['text', 'Social links: X / Twitter address (empty = hidden)', ''],
        'site_social_instagram' => ['text', 'Social links: Instagram address (empty = hidden)', ''],
        'site_social_facebook' => ['text', 'Social links: Facebook address (empty = hidden)', ''],
        'site_social_youtube' => ['text', 'Social links: YouTube address (empty = hidden)', ''],
    ]],

    'popup' => ['label' => 'Enquiry popup', 'section' => 'site', 'fields' => [
        'site_popup_on' => ['toggle', 'Popup: show it on the website', '1'],
        'site_popup_delay' => ['text', 'Popup: seconds before it appears', '2'],
        'site_popup_days' => ['text', 'Popup: do not show again for (days) after it is closed', '7'],
        'site_popup_eyebrow' => ['text', 'Text: small label', 'Free consultation'],
        'site_popup_title' => ['text', 'Text: heading', 'Get a *lease proposal* for your next asset'],
        'site_popup_text' => ['longtext', 'Text: sub-text', 'Tell us what you need to fund and a senior team member will get back to you within one working day.'],
        'site_popup_list' => ['lines', 'Text: bullet points, one per line', "100% funding, no down payment\nOff-balance sheet operating lease\nOne transparent price, no hidden fees"],
        'site_popup_btn' => ['text', 'Text: button', 'Request a call back'],
        'site_popup_note' => ['text', 'Text: small print under the button', 'We respect your privacy. Your details are only used to respond to this enquiry.'],
    ]],

    'home' => ['label' => 'Home page', 'fields' =>
        $seo('home', "Praxia – Asset-Backed Financing for India's Enterprises", "Praxia helps Indian mid-market corporates acquire moveable CapEx assets through off-balance-sheet operating leases. No upfront investment, one transparent price.") + [
        'home_hero_pill'  => ['text', 'Hero: pill text', 'Operating lease · Off-balance sheet · 100% funding'],
        'home_hero_title' => ['text', 'Hero: heading (typing words are added after it)', 'Fast asset financing for your'],
        'home_hero_words' => ['lines', 'Hero: typing words, one per line', "machinery\nfleet\nequipment\nsolar plant"],
        'home_hero_sub'   => ['longtext', 'Hero: sub-text', 'Praxia procures the assets you need and leases them to you: no down payment, rentals shaped to your cash cycle, one price and zero hidden fees.'],
        'home_path1_img'  => ['image', 'Card 1 (corporates): image', $U('1565793298595-6a879b1d9492', 1200, 75)],
        'home_path1_tag'  => ['text', 'Card 1: tag', 'For corporates'],
        'home_path1_title' => ['text', 'Card 1: heading', 'Lease the assets that grow your business'],
        'home_path1_text' => ['longtext', 'Card 1: text', 'Machinery, vehicles and equipment funded end to end, without touching your debt covenants.'],
        'home_path1_btn'  => ['text', 'Card 1: button', 'Explore leasing'],
        'home_path2_img'  => ['image', 'Card 2 (banks & NBFCs): image', $U('1560179707-f14e90ef3623', 1200, 75)],
        'home_path2_tag'  => ['text', 'Card 2: tag', 'For banks & NBFCs'],
        'home_path2_title' => ['text', 'Card 2: heading', 'Deploy capital into asset-backed mid-market demand'],
        'home_path2_text' => ['longtext', 'Card 2: text', 'We originate, structure and convert the customer. You get secured exposure and faster recovery.'],
        'home_path2_btn'  => ['text', 'Card 2: button', 'Partner with us'],
        'home_quote_title' => ['text', 'Quick quote box: heading', 'Get your lease quote in minutes'],
        'home_quote_types' => ['lines', 'Quick quote box: asset type options, one per line', "Industrial machinery\nVehicles & fleet\nRenewable energy\nElectronics / IT infra\nOther"],
        'home_quote_btn'  => ['text', 'Quick quote box: button', 'Get quote'],
        'home_trust_height' => ['text', 'Partner logos: logo height in pixels', '44'],
        'home_trust_gray' => ['toggle', 'Partner logos: show logos in grey (colour on hover)', '1'],
        'home_trust_speed' => ['text', 'Partner logos: seconds for one full scroll (higher = slower)', '40'],
        'home_trust_label' => ['text', 'Partner logos: caption', 'Deals executed & partnerships with leading financial institutions'],
        ] + $section('home', 'finance', 'What we finance', 'A smarter approach to *CapEx* funding') + [
        'home_finance_sub' => ['text', 'Finance: sub-text', "Operating lease solutions across India's most CapEx-intensive sectors."],
        'home_edu_img' => ['image', 'Lease vs loan: image', $U('1600880292203-757bb62b4baf', 900, 75)],
        'home_edu_eyebrow' => ['text', 'Lease vs loan: small label', 'Leasing vs. traditional loans'],
        'home_edu_title' => ['text', 'Lease vs loan: heading', 'Own the outcome, *not the debt*'],
        'home_edu_text' => ['longtext', 'Lease vs loan: text', 'Loans lock capital into ownership, covenants and rigid EMIs. An operating lease gives you the asset and the flexibility, and keeps your borrowing capacity free for growth.'],
        'home_edu_list' => ['lines', 'Lease vs loan: bullet points, one per line', "Lower upfront cost with no down payment\nReduced rates compared to long-term loans\nRentals structured monthly or quarterly\nNo asset depreciation risk\nOperating lease expenses are tax-deductible"],
        'home_edu_btn' => ['text', 'Lease vs loan: button', 'See how it works'],
        'home_edu_f1_big' => ['text', 'Floating badge 1: big text', '₹0'],
        'home_edu_f1_title' => ['text', 'Floating badge 1: title', 'Down payment'],
        'home_edu_f1_sub' => ['text', 'Floating badge 1: sub-text', '100% asset funding'],
        'home_edu_f2_big' => ['text', 'Floating badge 2: big text', '✓'],
        'home_edu_f2_title' => ['text', 'Floating badge 2: title', 'Off-balance'],
        'home_edu_f2_sub' => ['text', 'Floating badge 2: sub-text', 'No leverage impact'],
        ] + $section('home', 'features', 'Why Praxia', 'Everything a finance leader wants from a lease')
          + $section('home', 'process', 'How it works', 'From requirement to delivery in *weeks*')
          + $section('home', 'cases', 'Case studies', 'Real businesses. Real flexibility.') + [
        'home_cases_btn' => ['text', 'Case studies: button', 'View all case studies'],
        ] + $section('home', 'investors', 'Backed by', 'Strong investors, strong reserves')
          + $section('home', 'voices', 'Customer voices', 'Trusted by finance leaders')
          + $section('home', 'faq', 'FAQ', 'Quick answers') + [
        'home_faq_link' => ['text', 'FAQ: link text', 'See all FAQs →'],
        'home_apply_img' => ['image', 'Proposal section: background image', $U('1519389950473-47ba0277781c')],
        'home_apply_eyebrow' => ['text', 'Proposal section: small label', 'Get started'],
        'home_apply_title' => ['text', 'Proposal section: heading', 'Put your next asset to work, without the debt'],
        'home_apply_text' => ['longtext', 'Proposal section: text', 'Tell us what you need to fund. The Praxia team will come back with a structured lease proposal.'],
        'home_apply_list' => ['lines', 'Proposal section: bullet points, one per line', "Response from a senior team member\nMatched with the right bank or NBFC\nOne price, no hidden fees"],
        'home_apply_form_title' => ['text', 'Proposal form: heading', 'Request a proposal'],
        'home_apply_form_sub' => ['text', 'Proposal form: sub-text', "We'll reach out within one working day."],
        'home_apply_btn' => ['text', 'Proposal form: button', 'Get financed'],
    ]],

    'product' => ['label' => 'Product page', 'fields' =>
        $seo('product', 'Operating Lease Product – Praxia', 'Praxia operating lease: procure assets without owning them. No down payment, flexible rentals, tax-deductible, off-balance sheet.')
        + $hero('product', $U('1504917595217-d4dc5ebe6122'), 'Operating lease, engineered for growth', 'Praxia procures assets on your behalf and leases them to you with flexible repayment terms, minimising upfront capital expenditure.') + [
        'product_what_eyebrow' => ['text', 'What we do: small label', 'What we do'],
        'product_what_title' => ['text', 'What we do: heading', 'Acquire critical assets without the burden of *ownership*'],
        'product_what_text' => ['longtext', 'What we do: intro text', 'Conserve cash, reduce debt and focus on core operations while Praxia manages the process end to end:'],
        'product_what_list' => ['lines', 'What we do: bullet points, one per line', "Asset selection and procurement\nFlexible and customised repayment structures\nProcess optimisation and efficiency gains\nTransparent, hassle-free services\nSeamless documentation and execution"],
        'product_what_img' => ['image', 'What we do: image', $U('1581092160562-40aa08e78837', 900, 75)],
        'product_what_f_big' => ['text', 'Floating badge: big text', '1'],
        'product_what_f_title' => ['text', 'Floating badge: title', 'One price'],
        'product_what_f_sub' => ['text', 'Floating badge: sub-text', 'Zero hidden fees'],
        ] + $section('product', 'compare', 'Loan vs. lease', 'A cost-effective alternative to traditional financing') + [
        'product_compare_col1' => ['text', 'Comparison table: loan column title', 'Traditional loan'],
        'product_compare_col2' => ['text', 'Comparison table: lease column title', 'Praxia operating lease'],
        ] + $section('product', 'assets', 'Assets we fund', 'Moveable CapEx across core sectors')
          + $section('product', 'diligence', 'Our diligence', 'Every mandate is filtered on three aspects')
          + $section('product', 'process', 'Process', 'Four steps to your asset') + [
        'product_cta_title' => ['text', 'Bottom call-to-action: heading', 'Want a lease structured for your business?'],
        'product_cta_sub' => ['text', 'Bottom call-to-action: text', "Share your requirement and we'll come back with options."],
        'product_cta_btn' => ['text', 'Bottom call-to-action: button', 'Get a quote'],
    ]],

    'about' => ['label' => 'About page', 'fields' =>
        $seo('about', 'About Us & Team – Praxia', 'Meet the Praxia team: founders from PwC, HSBC, DBS, Amazon and Urban Company, backed by Whiteboard Capital, Alteria Capital and OG Capital.')
        + $hero('about', $U('1560179707-f14e90ef3623'), 'Transparency, simplicity and customer-centricity', 'Praxia (legal name: Novus Procura India Private Limited) specialises in customised leasing and asset financing for Indian corporates.') + [
        'about_story_img' => ['image', 'Our story: image', $U('1553877522-43269d4ea984', 900, 75)],
        'about_story_f_big' => ['text', 'Floating badge: big text', '22'],
        'about_story_f_title' => ['text', 'Floating badge: title', 'Founded 2022'],
        'about_story_f_sub' => ['text', 'Floating badge: sub-text', 'February'],
        'about_story_eyebrow' => ['text', 'Our story: small label', 'Our story'],
        'about_story_title' => ['text', 'Our story: heading', 'Built to make CapEx financing *simple*'],
        'about_story_p1' => ['longtext', 'Our story: paragraph 1', "With a deep understanding of India's business landscape and an extensive network of financiers, Praxia delivers asset-based solutions that optimise cash flow, reduce debt and enhance operational efficiency."],
        'about_story_p2' => ['longtext', 'Our story: paragraph 2', 'We are building a resilient, long-term business with a focus on operational efficiency, risk mitigation and financial prudence, investing in automated invoicing, CRM systems and internal controls to scale.'],
        'about_years_count' => ['text', 'Counter: combined years of experience (number)', '45'],
        'about_customers_count' => ['text', 'Counter: customers (number)', '15'],
        ] + $section('about', 'founders', 'Founders', 'Driven by a passionate team')
          + $section('about', 'leaders', 'Leadership', 'A strong second layer of management')
          + $section('about', 'backed', 'Backed by', 'Strong financial backing') + [
        'about_backed_sub' => ['longtext', 'Backed by: sub-text', 'Investors who bring capital, expertise and connections. A significant portion of funds is held in current and fixed deposit accounts.'],
        'about_cta_title' => ['text', 'Bottom call-to-action: heading', 'Want to work with us?'],
        'about_cta_sub' => ['text', 'Bottom call-to-action: text', 'As a customer, lender or teammate.'],
        'about_cta_btn1' => ['text', 'Bottom call-to-action: button 1', 'Contact us'],
        'about_cta_btn2' => ['text', 'Bottom call-to-action: button 2', 'See careers'],
    ]],

    'cases' => ['label' => 'Case studies page', 'fields' =>
        $seo('cases', 'Case Studies – Praxia', 'How Praxia helped an automotive OEM, a real estate leader, a construction equipment distributor and a dairy company with operating leases.')
        + $hero('cases', $U('1578575437130-527eed3abbec'), 'Real businesses. Real financial flexibility.', 'See how operating leases solved very different CapEx challenges.') + [
        'cases_cta_title' => ['text', 'Bottom call-to-action: heading', 'Your business could be our next case study'],
        'cases_cta_sub' => ['text', 'Bottom call-to-action: text', "Let's solve your CapEx challenge."],
        'cases_cta_btn' => ['text', 'Bottom call-to-action: button', 'Get in touch'],
    ]],

    'faq' => ['label' => 'FAQ page', 'fields' =>
        $seo('faq', 'FAQ – Praxia', 'Answers to common questions about Praxia operating leases, tax treatment, balance sheet impact and process.')
        + $hero('faq', $U('1553877522-43269d4ea984'), 'Questions, answered', 'Everything you need to know about leasing assets through Praxia.') + [
        'faq_cta_title' => ['text', 'Bottom call-to-action: heading', 'Still have questions?'],
        'faq_cta_sub' => ['text', 'Bottom call-to-action: text', 'Our team will walk you through a lease structure.'],
        'faq_cta_btn' => ['text', 'Bottom call-to-action: button', 'Talk to us'],
    ]],

    'careers' => ['label' => 'Careers page', 'fields' =>
        $seo('careers', 'Careers – Praxia', "Join Praxia and help build India's leading asset-backed financing platform.")
        + $hero('careers', $U('1600880292203-757bb62b4baf'), 'Build the future of CapEx financing', 'Join a small, ambitious team of ex-bankers, consultants and operators closing a USD 9 billion annual asset finance gap.')
        + $section('careers', 'why', 'Why Praxia', "Where you'll grow")
        + $section('careers', 'roles', 'Open roles', 'Current opportunities') + [
        'careers_form_title' => ['text', 'Application form: heading', 'Apply now'],
        'careers_form_sub' => ['text', 'Application form: text', 'Pick a role, or send an open application.'],
    ]],

    'contact' => ['label' => 'Contact page', 'fields' =>
        $seo('contact', 'Contact Us – Praxia', 'Get in touch with Praxia for an operating lease proposal.')
        + $hero('contact', $U('1519389950473-47ba0277781c'), "Let's structure your next asset", "Tell us what you need to fund. We'll respond with lease options and the right financier match.") + [
        'contact_reach_title' => ['text', 'Left column: heading', 'Reach out to Praxia'],
        'contact_reach_text' => ['longtext', 'Left column: text', 'Our team works with corporates and financial institutions across India.'],
        'contact_audiences' => ['lines', 'Form: "I am a" options, one per line', "Corporate looking for an asset lease\nBank / NBFC looking to partner\nOther"],
        'contact_btn' => ['text', 'Form: button', 'Send enquiry'],
    ]],
];
