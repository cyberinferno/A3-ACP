USE [ASD]
GO

/****** Object:  Table [dbo].[charac0]    Script Date: 06/07/2013 19:54:27 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[charac0](
	[c_id] [char](20) NOT NULL,
	[c_sheadera] [varchar](255) NOT NULL,
	[c_sheaderb] [varchar](255) NOT NULL,
	[c_sheaderc] [varchar](255) NOT NULL,
	[c_headera] [varchar](255) NOT NULL,
	[c_headerb] [varchar](255) NOT NULL,
	[c_headerc] [varchar](255) NOT NULL,
	[d_cdate] [smalldatetime] NULL,
	[d_udate] [smalldatetime] NULL,
	[c_status] [char](1) NOT NULL,
	[m_body] [varchar](4000) NOT NULL,
	[rb] [int] NOT NULL,
	[set_gift] [int] NOT NULL,
	[online] [char](10) NULL,
	[c_reset] [int] NOT NULL,
	[rc_event] [int] NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[charac0] ADD  CONSTRAINT [rb]  DEFAULT ('0') FOR [rb]
GO

ALTER TABLE [dbo].[charac0] ADD  CONSTRAINT [times_rb]  DEFAULT ('0') FOR [set_gift]
GO

ALTER TABLE [dbo].[charac0] ADD  CONSTRAINT [DF_charac0_c_reset]  DEFAULT ((1)) FOR [c_reset]
GO

ALTER TABLE [dbo].[charac0] ADD  CONSTRAINT [DF_charac0_rc_event]  DEFAULT ((1)) FOR [rc_event]
GO

